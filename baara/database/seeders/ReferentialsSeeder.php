<?php

namespace Database\Seeders;

use App\Models\ReferentialCommune;
use App\Models\ReferentialLanguage;
use App\Models\ReferentialLicense;
use App\Models\ReferentialSkill;
use Illuminate\Database\Seeder;

class ReferentialsSeeder extends Seeder
{
    public function run(): void
    {
        // Communes du Burkina Faso (principales)
        $communes = [
            ['name' => 'Ouagadougou',   'region' => 'Centre'],
            ['name' => 'Bobo-Dioulasso','region' => 'Hauts-Bassins'],
            ['name' => 'Koudougou',     'region' => 'Centre-Ouest'],
            ['name' => 'Ouahigouya',    'region' => 'Nord'],
            ['name' => 'Banfora',       'region' => 'Cascades'],
            ['name' => 'Dédougou',      'region' => 'Boucle du Mouhoun'],
            ['name' => 'Kaya',          'region' => 'Centre-Nord'],
            ['name' => 'Ziniaré',       'region' => 'Plateau-Central'],
            ['name' => 'Tenkodogo',     'region' => 'Centre-Est'],
            ['name' => 'Fada N\'Gourma','region' => 'Est'],
            ['name' => 'Gaoua',         'region' => 'Sud-Ouest'],
            ['name' => 'Diébougou',     'region' => 'Sud-Ouest'],
            ['name' => 'Manga',         'region' => 'Centre-Sud'],
            ['name' => 'Léo',           'region' => 'Centre-Sud'],
            ['name' => 'Réo',           'region' => 'Centre-Ouest'],
            ['name' => 'Nouna',         'region' => 'Boucle du Mouhoun'],
            ['name' => 'Kongoussi',     'region' => 'Centre-Nord'],
            ['name' => 'Koupéla',       'region' => 'Centre-Est'],
            ['name' => 'Zorgho',        'region' => 'Plateau-Central'],
            ['name' => 'Pô',            'region' => 'Centre-Sud'],
        ];

        foreach ($communes as $commune) {
            ReferentialCommune::firstOrCreate(['name' => $commune['name']], $commune);
        }

        // Langues
        $languages = [
            ['name' => 'Français',   'code' => 'fr'],
            ['name' => 'Mooré',      'code' => 'mos'],
            ['name' => 'Dioula',     'code' => 'dyu'],
            ['name' => 'Fulfuldé',   'code' => 'fub'],
            ['name' => 'Bissa',      'code' => 'bib'],
            ['name' => 'Gourmantché','code' => 'gur'],
            ['name' => 'Anglais',    'code' => 'en'],
        ];

        foreach ($languages as $lang) {
            ReferentialLanguage::firstOrCreate(['code' => $lang['code']], $lang);
        }

        // Compétences agroécologiques
        $skills = [
            // Production végétale
            ['name' => 'Maraîchage',                  'category' => 'Production végétale'],
            ['name' => 'Agroforesterie',               'category' => 'Production végétale'],
            ['name' => 'Agriculture biologique',       'category' => 'Production végétale'],
            ['name' => 'Cultures pluviales',           'category' => 'Production végétale'],
            ['name' => 'Pépinière',                    'category' => 'Production végétale'],
            ['name' => 'Arboriculture fruitière',      'category' => 'Production végétale'],
            // Élevage
            ['name' => 'Élevage bovin',                'category' => 'Élevage'],
            ['name' => 'Aviculture',                   'category' => 'Élevage'],
            ['name' => 'Apiculture',                   'category' => 'Élevage'],
            ['name' => 'Pisciculture',                 'category' => 'Élevage'],
            ['name' => 'Élevage ovin/caprin',          'category' => 'Élevage'],
            // Transformation
            ['name' => 'Transformation agro-alimentaire','category' => 'Transformation'],
            ['name' => 'Conservation des aliments',   'category' => 'Transformation'],
            ['name' => 'Bio-compostage',               'category' => 'Transformation'],
            ['name' => 'Bio-pesticides',               'category' => 'Transformation'],
            // Gestion des ressources
            ['name' => 'Irrigation',                   'category' => 'Ressources'],
            ['name' => 'Gestion de l\'eau',            'category' => 'Ressources'],
            ['name' => 'Énergies renouvelables',        'category' => 'Ressources'],
            ['name' => 'Gestion de la fertilité des sols','category' => 'Ressources'],
            // Autres
            ['name' => 'Gestion d\'exploitation',      'category' => 'Gestion'],
            ['name' => 'Comptabilité agricole',        'category' => 'Gestion'],
            ['name' => 'Marketing agricole',           'category' => 'Gestion'],
            ['name' => 'Mécanisation agricole',        'category' => 'Techniques'],
        ];

        foreach ($skills as $skill) {
            ReferentialSkill::firstOrCreate(['name' => $skill['name']], $skill);
        }

        // Permis de conduire
        $licenses = ['A', 'B', 'C', 'D'];
        foreach ($licenses as $name) {
            ReferentialLicense::firstOrCreate(['name' => $name]);
        }

        $this->command->info('✅ Référentiels insérés (communes, langues, compétences, permis).');
    }
}
