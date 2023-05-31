<?php

namespace App\Notifications\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountUpdatedNotification extends Notification
{
    use Queueable;

    private $_forum_groups = [];

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($forumGroups)
    {
        parent::__construct();

        $this->_forum_groups = $forumGroups;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Forum',
            'message' => 'Forenaccount wurde synchronisiert. Folgende Gruppen wurden zugeordnet: '.$this->forumGroupsToString(),
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
            'message' => 'Forenaccount wurde synchronisiert. Folgende Gruppen wurden zugeordnet: '.$this->forumGroupsToString(),
        ];
    }

    /**
     * Convert the forum groups array to a readable string
     * 
     * @return [type] [description]
     */
    private function forumGroupsToString() {
        $fgrps = \App\Models\Forum\ForumGroup::whereIn('forum_id', $this->_forum_groups)->select('name')->get();

        $result = '';
        foreach ($fgrps as $fg) {
            if($result == '') $result.= $fg->name;
            else $result.= ', '.$fg->name;
        }
        return $result;
    }
}
