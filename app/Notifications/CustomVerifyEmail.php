<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Setting;

class CustomVerifyEmail extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        $subject = Setting::get('verification_email_subject', 'Bitte bestätige deine E-Mail-Adresse');

        $defaultBody = "Hallo {name},\n\nbitte klicke auf den folgenden Link, um deine E-Mail-Adresse zu bestätigen und deinen Account freizuschalten:\n\n{verification_url}\n\nViele Grüße,\nDein Bücherei Team";
        $bodyTemplate = Setting::get('verification_email_body', $defaultBody);

        $body = str_replace(
        ['{name}', '{verification_url}'],
        [$notifiable->name, $verificationUrl],
            $bodyTemplate
        );

        $mailMessage = (new MailMessage)
            ->subject($subject);

        // Split by lines to add as text lines in the default Laravel mail template
        $lines = explode("\n", $body);
        foreach ($lines as $line) {
            if (trim($line) === '{verification_button}') {
                $mailMessage->action('E-Mail Bestätigen', $verificationUrl);
            }
            else {
                $mailMessage->line($line);
            }
        }

        return $mailMessage;
    }
}
