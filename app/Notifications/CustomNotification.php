<?php

namespace App\Notifications;

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
    public $action;

    public function __construct(
        string $title,
        string $body,
        ?array $action = null,
        array $modes = ['mail', 'database']
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->modes = $modes;
        $this->action = $action;
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
        $mail = (new MailMessage)
            ->greeting('skip')
            ->subject($this->title)
            ->line('Hello '.$notifiable->name.',')
            ->line(new HtmlString($this->body));

        if ($this->action) {
            $mail->action($this->action['text'], $this->action['url']);
        }

        return $mail;
    }
}
