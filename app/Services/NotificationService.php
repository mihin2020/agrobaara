<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Envoie un Mailable à tous les utilisateurs actifs ayant la permission donnée.
     *
     * @param string   $permissionSlug  Ex: 'notifications.contact', 'notifications.candidates'
     * @param Mailable $mailable        L'email à envoyer
     */
    public function notifyByPermission(string $permissionSlug, Mailable $mailable): void
    {
        $recipients = $this->getRecipients($permissionSlug);

        if ($recipients->isEmpty()) {
            return;
        }

        foreach ($recipients as $user) {
            Mail::to($user->email)->queue($mailable);
        }
    }

    /**
     * Retourne les utilisateurs actifs possédant la permission donnée
     * (directement ou via un rôle).
     */
    public function getRecipients(string $permissionSlug): Collection
    {
        $permission = Permission::where('slug', $permissionSlug)->first();

        if (!$permission) {
            return collect();
        }

        // Utilisateurs ayant la permission directement
        $directUserIds = $permission->users()
            ->where('status', UserStatus::Active)
            ->pluck('users.id');

        // Utilisateurs ayant la permission via un rôle
        $roleIds = $permission->roles()->pluck('roles.id');
        $viaRoleUserIds = User::where('status', UserStatus::Active)
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
            ->pluck('id');

        $allIds = $directUserIds->merge($viaRoleUserIds)->unique();

        return User::whereIn('id', $allIds)->get();
    }
}
