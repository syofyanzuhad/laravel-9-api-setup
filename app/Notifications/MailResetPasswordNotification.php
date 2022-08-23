<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
//custom
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class MailResetPasswordNotification extends Notification
{
    use Queueable;

    protected $pageUrl;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $pageUrl = null)
    {
        $this->token = $token;
        
        if ($pageUrl) {
            $this->pageUrl = $pageUrl;
        } else {
            $this->pageUrl = config('app.url') . '/reset-password';
        }
        // we can set whatever we want here, or use .env to set environmental variables
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::get('Reset Application Password'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), "$this->pageUrl/?token={$this->token}&email={$notifiable->email}")
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.users.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }
}