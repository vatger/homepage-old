<?php

namespace App\Notifications\ATD;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SoloModifiedNotification extends Notification
{
    use Queueable;

    private $_station;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($station)
    {
        $this->_station = $station;
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
            'message' => 'Deine Solofreigabe für '.$this->_station.' wurde verändert. Bitte informiere dich über die Änderungen.',
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
            'message' => 'Deine Solofreigabe für '.$this->_station.' wurde verändert. Bitte informiere dich über die Änderungen.',
        ];
    }
}
