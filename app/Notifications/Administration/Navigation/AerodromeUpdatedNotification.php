<?php

namespace App\Notifications\Administration\Navigation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Navigation\Aerodrome;

class AerodromeUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The updated aerodrome
     * @var Aerodrome
     */
    private $_aerodrome;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Aerodrome $aerodrome)
    {
        $this->_aerodrome = $aerodrome;
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
            'title' => 'Flugplatzdaten aktualisiert',
            'message' => 'Die Daten des Flugplatzes '.$this->_aerodrome->name.' wurden überarbeitet.',
            'aerodrome' => $this->_aerodrome,
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
            'title' => 'Flugplatzdaten aktualisiert',
            'message' => 'Die Daten des Flugplatzes '.$this->_aerodrome->name.' wurden überarbeitet.',
            'aerodrome' => $this->_aerodrome,
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
            'title' => 'Flugplatzdaten aktualisiert',
            'message' => 'Die Daten des Flugplatzes '.$this->_aerodrome->name.' wurden überarbeitet.',
            'aerodrome' => $this->_aerodrome,
        ];
    }
}
