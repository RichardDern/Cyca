<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class UnreadItemsChanged extends Notification
{
    use Queueable;

    /**
     * Feed items, feeds, documents or folders to recalculate unread items for.
     *
     * @var array
     */
    private $data = [];

    /**
     * Create a new notification instance.
     *
     * @param null|array Feed items, feeds, documents or folders to recalculate
     * unread items for
     * @param null|mixed $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return $notifiable->countUnreadItems($this->data);
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return (new BroadcastMessage($this->toArray($notifiable)))->onQueue('notifications');
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array
     */
    public function viaQueues()
    {
        return [
            'broadcast' => 'notifications',
        ];
    }
}
