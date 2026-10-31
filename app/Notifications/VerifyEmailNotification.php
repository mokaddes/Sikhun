<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function __construct(public string $verificationUrl) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bn = app()->getLocale() === 'bn';

        return (new MailMessage)
            ->subject($bn ? 'আপনার ইমেইল যাচাই করুন — Sikhun.com' : 'Verify your email — Sikhun.com')
            ->greeting($bn ? "স্বাগতম, {$notifiable->name}!" : "Welcome, {$notifiable->name}!")
            ->line($bn
                ? 'Sikhun.com-এ অ্যাকাউন্ট তৈরি করার জন্য ধন্যবাদ। ইমেইলটি যাচাই করলে পাসওয়ার্ড রিসেট ও গুরুত্বপূর্ণ নোটিফিকেশন নিরাপদে পাবেন।'
                : 'Thanks for creating a Sikhun.com account. Verifying your email keeps password resets and important notifications secure.')
            ->action($bn ? 'ইমেইল যাচাই করুন' : 'Verify email', $this->verificationUrl)
            ->line($bn
                ? 'লিংকটি ৬০ মিনিট পর্যন্ত কাজ করবে।'
                : 'This link will expire in 60 minutes.')
            ->line($bn
                ? 'আপনি অ্যাকাউন্ট না বানিয়ে থাকলে এই ইমেইলটি উপেক্ষা করুন।'
                : 'If you did not create an account, you can ignore this email.');
    }
}
