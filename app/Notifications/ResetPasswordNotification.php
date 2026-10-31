<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Locale-aware password reset mail.
 *
 * The default Laravel ResetPassword notification hard-codes the
 * `password.reset` route and English copy. Admins need a different route,
 * and Sikhun's audience is Bengali-first, so both are parameters here.
 */
class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
        public string $email,
        public string $routeName = 'password.reset',
        public int $expireMinutes = 60,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bn = app()->getLocale() === 'bn';
        $url = route($this->routeName, [
            'token' => $this->token,
            'email' => $this->email,
        ]);

        return (new MailMessage)
            ->subject($bn ? 'পাসওয়ার্ড রিসেট করুন — Sikhun.com' : 'Reset your password — Sikhun.com')
            ->greeting($bn ? "আসসালামু আলাইকুম, {$notifiable->name}" : "Hello, {$notifiable->name}")
            ->line($bn
                ? 'আপনার অ্যাকাউন্টের পাসওয়ার্ড রিসেট করার একটি অনুরোধ এসেছে।'
                : 'We received a request to reset the password for your account.')
            ->action($bn ? 'পাসওয়ার্ড রিসেট করুন' : 'Reset password', $url)
            ->line($bn
                ? "লিংকটি {$this->expireMinutes} মিনিট পর্যন্ত কাজ করবে।"
                : "This link will expire in {$this->expireMinutes} minutes.")
            ->line($bn
                ? 'আপনি অনুরোধ না করে থাকলে এই ইমেইলটি উপেক্ষা করুন — আপনার পাসওয়ার্ড বদলাবে না।'
                : 'If you did not request this, you can ignore this email — your password will not change.');
    }
}
