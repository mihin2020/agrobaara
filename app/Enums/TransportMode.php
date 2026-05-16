<?php

namespace App\Enums;

enum TransportMode: string
{
    case DeuxRoues = 'deux_roues';
    case Voiture   = 'voiture';
    case Autre     = 'autre';
    case Aucun     = 'aucun';

    public function label(): string
    {
        return match($this) {
            self::DeuxRoues => 'Deux-roues',
            self::Voiture   => 'Voiture',
            self::Autre     => 'Autre',
            self::Aucun     => 'Aucun',
        };
    }
}
