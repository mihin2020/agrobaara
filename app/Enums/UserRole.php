<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Moderateur = 'moderateur';
    case Operateur  = 'operateur';

    public function label(): string
    {
        return match($this) {
            self::SuperAdmin => 'Super Administrateur',
            self::Moderateur => 'Modérateur',
            self::Operateur  => 'Opérateur',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(
            fn(self $role) => ['value' => $role->value, 'label' => $role->label()],
            self::cases()
        );
    }
}
