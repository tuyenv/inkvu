<?php
namespace App\Helpers;
use App\Models\NotifySettings;

class NotifyHelper
{

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
        return $notifySettings->save();
    }

    public static function getNotifySetting($userId, $email)
    {
        $notifySettings = NotifySettings::where('notify_user', $userId)
            ->where('email', $email)
            ->first();

        return $notifySettings;
    }
}
