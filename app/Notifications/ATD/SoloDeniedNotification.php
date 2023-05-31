<?php

namespace App\Notifications\ATD;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SoloDeniedNotification extends Notification
{
    use Queueable;

    private $station;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($station)
    {
        $this->station = $station;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'title' => 'ATD Verwaltung',
            'message' => 'Deine Solofreigabe für '.$this->station.' wurde abgelehnt.',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'ATD Verwaltung',
            'message' => 'Deine Solofreigabe für '.$this->station.' wurde abgelehnt.',
        ];
    }
}
