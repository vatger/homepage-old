<?php

namespace App\Notifications\Membership;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TeamSpeakIdentityCreated extends Notification implements ShouldQueue
{
    use Queueable;

    private $_tsid;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($tsid)
    {
        $this->_tsid = $tsid;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Get the broadcast representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'TeamSpeak Identity Manager',
            'message' => 'Die ID ' . $this->_tsid . ' wurde mit deinem Account verbunden.'
        ]);
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
            'title' => 'TeamSpeak Identity Manager',
            'message' => 'Die ID ' . $this->_tsid . ' wurde mit deinem Account verbunden.'
        ];
    }
}
