<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CustomNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $body;
    public $modes;
    public $actions;

    public function __construct(
        string $title,
        string $body,
        array $actions = [],
        array $modes = ['mail', 'database']
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->modes = $modes;
        $this->actions = $actions;
    }

    public function via($notifiable): array
    {
        return $this->modes;
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $user = $this->notifyingUser($notifiable);
        $mailMessage = (new MailMessage)
            ->subject($this->title)
            ->view('mails.default', [
                'body' => $this->body,
                'actions' => $this->actions,
                'user' => $user
            ]);
        return $user ? $mailMessage->from($user->email, $user->name) : $mailMessage;
    }

    private function notifyingUser($notifiable) {
        if ($notifiable instanceof User || $notifiable instanceof Customer) {
            return $notifiable instanceof User ? $notifiable : $notifiable->user;
        }

        return null;
    }
}
