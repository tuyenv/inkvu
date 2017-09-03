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
        $notifySettings = self::getNotifySetting($payload['push_notify_user'], $payload['push_email'], $payload['push_notify_user']);
        if (!($notifySettings instanceof NotifySettings)) {
            $notifySettings = new NotifySettings();
        }

        $notifySettings->notify_user = $payload['push_notify_user'];
        $notifySettings->email = $payload['push_email'];
        $notifySettings->mobile = $payload['push_mobile'];
        $notifySettings->web_notify = $payload['push_web_check'] == 'true' ? 1 : 0;
        $notifySettings->mobile_notify = $payload['push_mobile_check'] == 'true' ? 1 : 0;
        $notifySettings->email_notify = $payload['push_email_check'] == 'true' ? 1 : 0;
        return $notifySettings->save();
    }

    public static function getNotifySetting($userId, $email, $nofityUser)
    {
        $notifySettings = NotifySettings::where('notify_user', $userId)
            ->where('email', $email)
            ->where('notify_user', $nofityUser)
            ->first();

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

            if ($setting['web_notify']) {
                $arrInsert[] = array_merge($arrBase, array('push_type' => self::PUSH_TYPE_WEB));
            }

            if ($setting['mobile_notify']) {
                $arrInsert[] = array_merge($arrBase, array('push_type' => self::PUSH_TYPE_MOBILE));
            }

            if ($setting['email_notify']) {
                $arrInsert[] = array_merge($arrBase, array('push_type' => self::PUSH_TYPE_EMAIL));
            }
        }

        return NotifyDeliver::insert($arrInsert);
    }
}
