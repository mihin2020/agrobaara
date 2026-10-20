<?php

namespace App\Enums;

enum OfferStatus: string
{
    case Brouillon  = 'brouillon';
    case EnAttente  = 'en_attente';
    case Publiee    = 'publiee';
    case Archivee   = 'archivee';

    public function label(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::EnAttente => 'En attente de validation',
            self::Publiee   => 'Publiée',
            self::Archivee  => 'Archivée',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Brouillon => 'bg-amber-100 text-amber-800',
            self::EnAttente => 'bg-orange-100 text-orange-800',
            self::Publiee   => 'bg-green-100 text-green-800',
            self::Archivee  => 'bg-gray-100 text-gray-600',
        };
    }
}
