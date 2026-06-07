<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class JourneyActivityReminder extends Notification
{
    use Queueable;

    public $activity;

    public function __construct($activity)
    {
        $this->activity = $activity;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Upcoming Activity')
            ->icon('/icons/icon-192x192.png')
            ->body("Your activity '{$this->activity->title}' is starting in 15 minutes!")
            ->action('View Journey', 'view_journey')
            ->data(['url' => "/journey/{$this->activity->trip->journey->id}"]);
    }
}
