<?php

namespace App\Enums;

enum CompanyStatus: string
{
    case GIE                  = 'gie';
    case SA                   = 'sa';
    case SARL                 = 'sarl';
    case SCOOP                = 'scoop';
    case EntrepriseIndividuelle = 'entreprise_individuelle';
    case SAS                  = 'sas';
    case Autre                = 'autre';

    public function label(): string
    {
        return match($this) {
            self::GIE                   => 'GIE',
            self::SA                    => 'SA',
            self::SARL                  => 'SARL',
            self::SCOOP                 => 'SCOOP',
            self::EntrepriseIndividuelle => 'Entreprise individuelle',
            self::SAS                   => 'SAS',
            self::Autre                 => 'Autre',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $s) => ['value' => $s->value, 'label' => $s->label()],
            self::cases()
        );
    }
}
