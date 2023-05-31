<?php

namespace App\Notifications\ATD;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NoMemberOfRegionalgroupNotification extends Notification
{
    use Queueable;

    private $_rgName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($rgName)
    {
        $this->_rgName = $rgName;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return [
            'title' => 'ATD Verwaltung',
            'message' => 'Du bist kein Mitglied oder Gastmitglied der Regionalgruppe '.$this->_rgName.'.',
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
            'message' => 'Du bist kein Mitglied oder Gastmitglied der Regionalgruppe '.$this->_rgName.'.',
        ];
    }
}
