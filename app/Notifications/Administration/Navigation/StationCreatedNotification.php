<?php

namespace App\Notifications\Administration\Navigation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Navigation\Station;

class StationCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $_station;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Station $station)
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
        // return ['database', 'broadcast'];
        return ['broadcast']; // Only broadcast to the causer of the event
    }

    /**
    * Get the broadcastable representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return BroadcastMessage
    */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Station angelegt',
            'message' => 'Die Station '.$this->_station->ident.' wurde erfolgreich angelegt.',
            'station' => $this->_station,
        ]);
    }

    /**
     * Get the database format of the notification
     * 
     * @param  [type] $notifiable [description]
     * @return [type]             [description]
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Station angelegt',
            'message' => 'Die Station '.$this->_station->ident.' wurde erfolgreich angelegt.',
            'station' => $this->_station,
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
            'title' => 'Station angelegt',
            'message' => 'Die Station '.$this->_station->ident.' wurde erfolgreich angelegt.',
            'station' => $this->_station,
        ];
    }
}
