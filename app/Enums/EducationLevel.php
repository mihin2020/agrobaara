<?php

namespace App\Enums;

enum EducationLevel: string
{
    case Secondaire = 'secondaire';
    case Licence    = 'licence';
    case Master     = 'master';
    case Autre      = 'autre';

    public function label(): string
    {
        return match($this) {
            self::Secondaire => 'Secondaire',
            self::Licence    => 'Licence',
            self::Master     => 'Master',
            self::Autre      => 'Autre',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $e) => ['value' => $e->value, 'label' => $e->label()],
            self::cases()
        );
    }
}
