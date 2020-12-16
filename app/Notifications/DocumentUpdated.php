<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentUpdated extends Notification
{
    use Queueable;

    /**
     * Updated document.
     *
     * @var \App\Models\Document
     */
    public $document;

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
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
        $this->document->loadMissing('feeds');

        return ['document' => $this->document->toArray()];
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array
     */
    public function viaQueues()
    {
        return [
            'broadcast'  => 'notifications',
        ];
    }
}
