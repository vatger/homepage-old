<?php

namespace App\Notifications\Administration\Regionalgroups;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

use App\Models\Regionalgroups\Regionalgroup;

class RequestDeniedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $_regionalgroup;
    private $_details;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Regionalgroup $regionalgroup, $details = null)
    {
        $this->_regionalgroup = $regionalgroup;
        $this->_details = $details;
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

    /**
    * Get the broadcastable representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return BroadcastMessage
    */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Regionalgruppenanfrage',
            'message' => 'Die Anfrage an die Regionalgruppe '.$this->_regionalgroup->name.' wurde abgelehnt.',
            'regionalgroup' => $this->_regionalgroup->only(['id', 'name']),
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
            'title' => 'Regionalgruppenanfrage',
            'message' => 'Die Anfrage an die Regionalgruppe '.$this->_regionalgroup->name.' wurde abgelehnt.',
            'details' => $this->_details,
            'regionalgroup' => $this->_regionalgroup->only(['id', 'name']),
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
            'title' => 'Regionalgruppenanfrage',
            'message' => 'Die Anfrage an die Regionalgruppe '.$this->_regionalgroup->name.' wurde abgelehnt.',
            'details' => $this->_details,
            'regionalgroup' => $this->_regionalgroup->only(['id', 'name']),
        ];
    }
}
