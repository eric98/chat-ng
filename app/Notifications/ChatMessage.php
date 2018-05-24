<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class ChatMessage extends Notification
{
    use Queueable;

    public $user;
    public $chat;
    public $text;
    public $created_at;

    /**
     * ChatMessage constructor.
     * @param $user
     * @param $text
     * @param $created_at
     */
    public function __construct($user, $chat, $text, $created_at)
    {
        $this->user = $user;
        $this->chat = $chat;
        $this->text = $text;
        $this->created_at = $created_at;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast',WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'user' => $this->user,
            'chat' => $this->chat,
            'text' => $this->text,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Get the web push representation of the notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title($this->chat->name)
            ->icon($this->user['avatar'])
            ->body($this->user['name'])
            ->action('Veure Aplicació', 'view_app')
            ->data(['id' => $notification->id]);
    }
}
