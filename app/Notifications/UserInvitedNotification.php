<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $token,
        private readonly User   $invitedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Invitation — Agro Eco BAARA Back-office')
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Votre compte d'accès au back-office **Agro Eco BAARA** a été créé par {$this->invitedBy->full_name}.")
            ->line('Cliquez sur le bouton ci-dessous pour définir votre mot de passe et activer votre compte.')
            ->action('Définir mon mot de passe', $url)
            ->line('Ce lien expirera dans **60 minutes**. Contactez votre administrateur si vous avez besoin d\'un nouveau lien.')
            ->salutation('L\'équipe Agro Eco BAARA');
    }
}