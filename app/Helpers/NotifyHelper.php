<?php
namespace App\Helpers;
use App\Models\NotifySettings;
use App\Models\NotifyQueue;
use App\Models\NotifyDeliver;

class NotifyHelper
{
    const STATUS_INIT = 'init';
    const STATUS_PROCESS = 'process';
    const STATUS_FAILED = 'failed';
    const STATUS_DONE = 'done';

    const PUSH_TYPE_WEB = 'web';
    const PUSH_TYPE_EMAIL = 'email';
    const PUSH_TYPE_MOBILE = 'mobile';

    public static function saveNotification($payload)
    {
        $notifySettings = self::getNotifySetting($payload['push_notify_user'], $payload['push_email']);
        if (!($notifySettings instanceof NotifySettings)) {
            $notifySettings = new NotifySettings();
        }

        $notifySettings->notify_user = $payload['push_notify_user'];
        $notifySettings->email = $payload['push_email'];
        $notifySettings->mobile = $payload['push_mobile'];
        $notifySettings->web_notify = $payload['push_web_check'] == 'true' ? 1 : 0;
        $notifySettings->mobile_notify = $payload['push_mobile_check'] == 'true' ? 1 : 0;
        $notifySettings->email_notify = $payload['push_email_check'] == 'true' ? 1 : 0;
        $notifySettings->web_push_userid = $payload['push_web_userid'];
        if (session('userId')) {
            $notifySettings->creator = session('userId');
        }
        $notifySettings->save();
        return $notifySettings;
    }

    public static function updateNotification($payload)
    {
        $notifySettings = NotifySettings::where('id', $payload['id'])->first();
        if (!($notifySettings instanceof NotifySettings)) {
            return false;
        }

        $notifySettings->web_push_userid = $payload['push_web_userid'];
        return $notifySettings->save();
    }

    public static function getNotifySetting($userId)
    {
        $notifySettings = NotifySettings::where('notify_user', $userId)
            ->where('creator', session('userId'))
            ->first();

        return $notifySettings;
    }

    public static function getNotifySettingByUser($userId, $where)
    {
        $notifySettings = NotifySettings::where('notify_user', $userId)
            ->where($where['key'], $where['value'])
            ->get()->toArray();

        return $notifySettings;
    }

    public static function saveNotifyQueueInit($linkId)
    {
        $notifyQueue = new NotifyQueue();
        $notifyQueue->user_id = session('userId');
        $notifyQueue->link_id = $linkId;
        $notifyQueue->status = self::STATUS_INIT;
        return $notifyQueue->save();
    }

    public static function saveNotifyQueueDone($linkId)
    {
        $notifyQueue = new NotifyQueue();
        $notifyQueue->user_id = session('userId');
        $notifyQueue->link_id = $linkId;
        $notifyQueue->status = self::STATUS_DONE;
        $notifySave = $notifyQueue->save();

        if ($notifySave) {
            self::saveNotifyDeliver(session('userId'), $linkId);
        }
        return $notifySave;
    }

    public static function saveNotifyDeliver($userId, $linkId)
    {
        $notifySettings = NotifySettings::where('notify_user', $userId)->get();
        if (empty($notifySettings)) {
            return false;
        }

        $arrInsert = array();
        foreach ($notifySettings as $setting) {
            $arrBase = array(
                'user_id' => $userId,
                'link_id' => $linkId,
                'status' => self::STATUS_INIT,
                'email' => $setting['email'],
                'mobile' => $setting['mobile'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            if ($setting['mobile_notify']) {
                $arrInsert[] = array_merge($arrBase, array('push_type' => self::PUSH_TYPE_MOBILE));
            }

            if ($setting['email_notify']) {
                $arrInsert[] = array_merge($arrBase, array('push_type' => self::PUSH_TYPE_EMAIL));
            }
        }

        return NotifyDeliver::insert($arrInsert);
    }

    public static function sendMessageWebPush($linkObject, $shortUrl)
    {
        $notifySend = self::getNotifySettingByUser(session('userId'), ['key' => 'web_notify', 'value' => 1]);
        if (empty($notifySend)) {
            return false;
        }

        $notifySettingPlayerID = [];
        foreach ($notifySend as $item) {
            if (!empty($item['web_push_userid'])) {
                $notifySettingPlayerID[] = $item['web_push_userid'];
            }
        }

        if (empty($notifySettingPlayerID)) {
            return false;
        }

        $content = array(
            "title" => $linkObject->title,
            "en" => $linkObject->description
        );

        $fields = array(
            'app_id' => env('WEB_PUSH_APP_ID'),
            'include_player_ids' => $notifySettingPlayerID,
            'url' => $shortUrl,
            'contents' => $content,
            'chrome_web_image' => $linkObject->image,
        );

        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('WEB_PUSH_REST_API')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function getNotifyDelivery($type, $limit)
    {
        $deliver = NotifyDeliver::where('push_type', $type)
            ->where('status', 'init')
            ->orderBy('id', 'ASC')
            ->limit($limit)
            ->get();

        return $deliver;
    }
}
