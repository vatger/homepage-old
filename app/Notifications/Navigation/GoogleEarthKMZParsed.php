<?php

namespace App\Notifications\Navigation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GoogleEarthKMZParsed extends Notification
{
    use Queueable;

    private $_sectorFileLocation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($fileName)
    {
        $this->_sectorFileLocation = $fileName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the broadcast representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toBroadcast($notifiable)
    {
        return [
            'title' => 'GoogleEarth Parsed',
            'message' => 'The parsing of the GE file is completed. See notifications details for further information.',
        ];
    }

    /**
     * Get the database format for the notification
     * 
     * @param  [type] $notifiable [description]
     * @return [type]             [description]
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'GoogleEarth Parsed',
            'message' => 'The parsing of the GE file is completed. See notifications details for further information.',
            'details' => 'Download results from here: <a href="/administration/download/'.urlencode($this->_sectorFileLocation).'" class="btn btn-xs btn-primary">Download</a>'
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
            //
        ];
    }
}
