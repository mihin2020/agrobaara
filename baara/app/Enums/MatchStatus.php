<?php

namespace App\Enums;

enum MatchStatus: string
{
    case Proposee  = 'proposee';
    case Contactee = 'contactee';
    case Entretien = 'entretien';
    case Acceptee  = 'acceptee';
    case Refusee   = 'refusee';
    case Abandonnee = 'abandonnee';

    public function label(): string
    {
        return match($this) {
            self::Proposee   => 'Proposée',
            self::Contactee  => 'Contactée',
            self::Entretien  => 'Entretien',
            self::Acceptee   => 'Acceptée',
            self::Refusee    => 'Refusée',
            self::Abandonnee => 'Abandonnée',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Proposee   => 'bg-blue-100 text-blue-800',
            self::Contactee  => 'bg-yellow-100 text-yellow-800',
            self::Entretien  => 'bg-purple-100 text-purple-800',
            self::Acceptee   => 'bg-green-100 text-green-800',
            self::Refusee    => 'bg-red-100 text-red-800',
            self::Abandonnee => 'bg-gray-100 text-gray-600',
        };
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::Acceptee, self::Refusee, self::Abandonnee]);
    }
}
