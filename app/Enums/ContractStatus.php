<?php

namespace App\Enums;

enum ContractStatus: string
{
    case Brouillon = 'brouillon';
    case Actif     = 'actif';
    case Clos      = 'clos';

    public function label(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Actif     => 'Actif',
            self::Clos      => 'Clos',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Brouillon => 'bg-amber-100 text-amber-800',
            self::Actif     => 'bg-green-100 text-green-800',
            self::Clos      => 'bg-gray-100 text-gray-600',
        };
    }
}
