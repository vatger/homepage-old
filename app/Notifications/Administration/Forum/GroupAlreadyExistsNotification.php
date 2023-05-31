<?php

namespace App\Notifications\Administration\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GroupAlreadyExistsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $_forumgroupId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->_forumgroupId = $id;
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
            'title' => 'Forumgruppen Manager',
            'message' => 'Die Forengruppe mit der ID '.$this->_forumgroupId.' existiert bereits. Sie kann kein weiteres mal angelegt werden.',
            'station' => $this->_forumgroupId,
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
            'title' => 'Forumgruppen Manager',
            'message' => 'Die Forengruppe mit der ID '.$this->_forumgroupId.' existiert bereits. Sie kann kein weiteres mal angelegt werden.',
            'station' => $this->_forumgroupId,
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
            'title' => 'Forumgruppen Manager',
            'message' => 'Die Forengruppe mit der ID '.$this->_forumgroupId.' existiert bereits. Sie kann kein weiteres mal angelegt werden.',
            'station' => $this->_forumgroupId,
        ];
    }
}