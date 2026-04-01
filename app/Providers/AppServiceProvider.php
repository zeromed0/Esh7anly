<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // إجبار HTTPS في الإنتاج
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // تعطيل Tracking لرابط التحقق (حل مشكلة Invalid Signature)
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verify Your Email Address')
                ->line('Please click the button below to verify your email address.')
                ->action('Verify Email', $url)
                ->line('If you did not create an account, no further action is required.')
                ->withSymfonyMessage(function ($message) {
                    $message->getHeaders()->addTextHeader('X-Mailin-custom', 'tracking:0');
                });
        });

        Vite::prefetch(concurrency: 3);
    }
}