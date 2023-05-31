<?php

namespace App\Notifications\Membership;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TeamSpeakRegistrationCreated extends Notification implements ShouldQueue
{
    use Queueable;

    private $_ip;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ip)
    {
        $this->_ip = $ip;
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
            'message' => 'Eine neue TeamSpeak Registrierung wurde für deinen Account angefragt. Absender: ' . $this->_ip
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
            'message' => 'Eine neue TeamSpeak Registrierung wurde für deinen Account angefragt. Absender: ' . $this->_ip
        ];
    }
}
