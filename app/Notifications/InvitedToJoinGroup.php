<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class InvitedToJoinGroup extends Notification implements ShouldQueue
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
     * @param \App\Models\User $user Inviting user
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
            ->from($this->user->email)
            ->line(sprintf('Hello there ! You have been invited by %s (%s) to join the %s group in Cyca.', $this->user->name, $this->user->email, $this->group->name))
            ->action('Accept invitation', URL::signedRoute('group.signed_accept_invitation', ['group' => $this->group->id]))
            ->line('If you already have an account on Cyca, you can decline this invitation in your user account.')
            ->line('If you do not already have an account on Cyca, you can register then click again on this link. If you do not want to join the group, you can safely ignore this message.')
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
