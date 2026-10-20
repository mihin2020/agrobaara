<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin       = 'super_admin';
    case Moderateur       = 'moderateur';
    case Operateur        = 'operateur';
    case Candidat         = 'candidat';
    case Entreprise       = 'entreprise';
    case CentreFormation  = 'centre_formation';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin      => 'Super Administrateur',
            self::Moderateur      => 'Modérateur',
            self::Operateur       => 'Opérateur',
            self::Candidat        => 'Candidat',
            self::Entreprise      => 'Entreprise',
            self::CentreFormation => 'Centre de formation',
        };
    }

    public function isPortal(): bool
    {
        return in_array($this, [self::Candidat, self::Entreprise, self::CentreFormation], true);
    }

    public function isStaff(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Moderateur, self::Operateur], true);
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
