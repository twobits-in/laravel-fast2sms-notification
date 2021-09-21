<?php

namespace NotificationChannels\Fast2sms;

use Illuminate\Notifications\Notification;
use TwoBitsIn\Fast2sms\Exceptions\CouldNotSendNotification as ExceptionsCouldNotSendNotification;
use TwoBitsIn\Fast2sms\Fast2smsClient;

class Fast2smsChannel
{
    protected $Fast2smsClient;
    public function __construct(Fast2smsClient $Fast2smsClient)
    {
        $this->Fast2smsClient = $Fast2smsClient;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Fast2sms\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('jsksms', $notification)) {
            return;
        }

        $message = $notification->toJsksms($notifiable);

        if (is_string($message)) {
            $message = new Fast2smsMessage($message);
        }

        if (mb_strlen($message->content) > $this->character_limit_count) {
            throw ExceptionsCouldNotSendNotification::contentLengthLimitExceeded($this->character_limit_count);
        }

        return $this->jsksms->send(trim($message->content),$to);

        
    }
}
