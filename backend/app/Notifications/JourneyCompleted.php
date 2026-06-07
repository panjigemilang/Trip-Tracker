<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class JourneyCompleted extends Notification
{
    use Queueable;

    public $journey;

    public function __construct($journey)
    {
        $this->journey = $journey;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Journey Completed')
            ->icon('/icons/icon-192x192.png')
            ->body("Congratulations! You've completed '{$this->journey->trip->title}'.")
            ->action('View Summary', 'view_summary')
            ->data(['url' => "/trips/{$this->journey->trip_id}"]);
    }
}
