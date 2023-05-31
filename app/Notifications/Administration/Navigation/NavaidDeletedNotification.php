<?php

namespace App\Notifications\Administration\Navigation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NavaidDeletedNotification extends Notification
{
    use Queueable;

    private $_navaid;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $navaid)
    {
        $this->_navaid = $navaid;
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
            'title' => 'Navaid gelöscht',
            'message' => 'Das Navaid '.$this->_navaid.' wurde erfolgreich gelöscht.',
            'navaid' => $this->_navaid,
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
            'title' => 'Navaid gelöscht',
            'message' => 'Das Navaid '.$this->_navaid.' wurde erfolgreich gelöscht.',
            'navaid' => $this->_navaid,
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
            'title' => 'Navaid gelöscht',
            'message' => 'Das Navaid '.$this->_navaid.' wurde erfolgreich gelöscht.',
            'navaid' => $this->_navaid,
        ];
    }
}
