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

        $lines = explode("\n", $body);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.verify', [
            'lines' => $lines,
            'verificationUrl' => $verificationUrl
        ]);
    }
}
