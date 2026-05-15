<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active          = 'active';
    case Inactive        = 'inactive';
    case Locked          = 'locked';
    case PendingPassword = 'pending_password';

    public function label(): string
    {
        return match($this) {
            self::Active          => 'Actif',
            self::Inactive        => 'Inactif',
            self::Locked          => 'Verrouillé',
            self::PendingPassword => 'Première connexion',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Active          => 'green',
            self::Inactive        => 'gray',
            self::Locked          => 'orange',
            self::PendingPassword => 'blue',
        };
    }
}
