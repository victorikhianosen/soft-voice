<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public array $actions = [],
        public array $modes = ['mail', 'database'],
        public bool $hasCustomSender = false
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
                'body'            => $this->body,
                'actions'         => $this->actions,
                'user'            => $user,
                'hasCustomSender' => $this->hasCustomSender,
            ]);
        return ($user && $this->hasCustomSender)
            ? $mailMessage->from($user->email, $user->name)
            : $mailMessage;
    }

    private function notifyingUser($notifiable) {
        if ($notifiable instanceof User || $notifiable instanceof Customer) {
            return $notifiable instanceof User ? $notifiable : $notifiable->user;
        }

        return null;
    }
}
