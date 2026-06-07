<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class JourneyActivityStarting extends Notification
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
            ->title('Activity Starting Now')
            ->icon('/icons/icon-192x192.png')
            ->body("It's time for '{$this->activity->title}'!")
            ->action('View Journey', 'view_journey')
            ->data(['url' => "/journey/{$this->activity->trip->journey->id}"]);
    }
}
