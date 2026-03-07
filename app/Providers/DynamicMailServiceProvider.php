<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class DynamicMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $mailConfig = [
                    'transport' => 'smtp',
                    'host' => Setting::get('smtp_host', env('MAIL_HOST', '127.0.0.1')),
                    'port' => Setting::get('smtp_port', env('MAIL_PORT', '2525')),
                    'encryption' => Setting::get('smtp_encryption', env('MAIL_ENCRYPTION', 'tls')),
                    'username' => Setting::get('smtp_user', env('MAIL_USERNAME')),
                    'password' => Setting::get('smtp_password', env('MAIL_PASSWORD')),
                    'timeout' => null,
                    'auth_mode' => null,
                ];

                Config::set('mail.mailers.smtp', $mailConfig);

                $fromAddress = Setting::get('mail_from_address', env('MAIL_FROM_ADDRESS', 'hello@example.com'));
                $fromName = Setting::get('mail_from_name', env('MAIL_FROM_NAME', 'Bücherei'));

                Config::set('mail.from', [
                    'address' => $fromAddress,
                    'name' => $fromName
                ]);
            }
        }
        catch (\Exception $e) {
        // Fail silently during e.g., initial setup or migrations
        }
    }
}
