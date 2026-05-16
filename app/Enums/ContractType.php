<?php

namespace App\Enums;

enum ContractType: string
{
    case Salarie        = 'salarie';
    case Saisonnier     = 'saisonnier';
    case Ponctuel       = 'ponctuel';
    case Apprentissage  = 'apprentissage';
    case Entrepreneuriat = 'entrepreneuriat';
    case Autre          = 'autre';

    public function label(): string
    {
        return match($this) {
            self::Salarie         => 'Emploi salarié',
            self::Saisonnier      => 'Contrat saisonnier',
            self::Ponctuel        => 'Mission ponctuelle',
            self::Apprentissage   => 'Apprentissage',
            self::Entrepreneuriat => 'Entrepreneuriat',
            self::Autre           => 'Autre',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label()],
            self::cases()
        );
    }
}
