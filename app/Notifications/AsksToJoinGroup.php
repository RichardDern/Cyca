<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class AsksToJoinGroup extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Inviting user.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Group to invite a user in.
     *
     * @var \App\Models\Group
     */
    protected $group;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\User $user User asking to join group
     */
    public function __construct(User $user, Group $group)
    {
        $this->user  = $user;
        $this->group = $group;
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->from(env('MAIL_FROM_ADDRESS'))
            ->line(sprintf('Hello there ! %s wants to join a group you created in Cyca: %s.', $this->user->name, $this->group->name))
            ->action(sprintf('Accept %s in %s', $this->user->name, $this->group->name), URL::signedRoute('group.signed_approve_user', ['user' => $this->user->id, 'group' => $this->group->id]))
            ->line('You can safely ignore this email if you prefer to decline.')
            ->line('Thank you for using our application!');
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
        return [
        ];
    }
}
