<?php

namespace App\Enums;

enum EntityType: string
{
    case Entreprise = 'entreprise';
    case CentreFormation = 'centre_formation';

    public function label(): string
    {
        return match ($this) {
            self::Entreprise       => 'Entreprise',
            self::CentreFormation  => 'Centre de formation',
        };
    }
}
