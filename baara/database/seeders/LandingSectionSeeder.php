<?php

namespace Database\Seeders;

use App\Models\LandingSection;
use Illuminate\Database\Seeder;

class LandingSectionSeeder extends Seeder
{
    private array $sections = [
        [
            'slug'         => 'hero',
            'type'         => 'hero',
            'title'        => 'Hero',
            'order_index'  => 1,
            'is_active'    => true,
            'always_visible' => true,
            'content'      => [
                'title'               => 'Connecter les talents et les opportunités en agroécologie',
                'subtitle'            => 'Le guichet unique pour l\'insertion professionnelle et le recrutement spécialisé dans l\'agriculture durable au Burkina Faso.',
                'cta_primary_text'    => 'Nous contacter',
                'cta_primary_link'    => '#contact',
                'cta_secondary_text'  => 'Découvrir nos services',
                'cta_secondary_link'  => '#services',
                'image_url'           => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=1600&q=80',
            ],
        ],
        [
            'slug'        => 'pour_qui',
            'type'        => 'pour_qui',
            'title'       => 'Pour qui ?',
            'order_index' => 2,
            'is_active'   => true,
            'always_visible' => false,
            'content'     => [
                'title' => 'Pour qui ?',
                'cards' => [
                    [
                        'key'         => 'candidats',
                        'title'       => 'Jeunes & Candidats',
                        'description' => 'Vous êtes passionné par l\'agroécologie ? Trouvez des opportunités concrètes d\'emploi et de formation pour bâtir votre carrière dans le secteur vert.',
                        'list'        => ['Accès aux offres exclusives', 'Accompagnement personnalisé', 'Formations certifiantes'],
                        'cta_text'    => 'Déposer mon CV',
                    ],
                    [
                        'key'         => 'entreprises',
                        'title'       => 'Entreprises & Fermes',
                        'description' => 'Optimisez vos recrutements avec des talents qualifiés. Nous vous aidons à identifier les profils techniques et managériaux dont vous avez besoin.',
                        'list'        => ['Présélection de candidats', 'Appui à la définition de postes', 'Plateforme de gestion d\'offres'],
                        'cta_text'    => 'Publier une offre',
                    ],
                ],
            ],
        ],
        [
            'slug'        => 'services',
            'type'        => 'services',
            'title'       => 'Nos Services',
            'order_index' => 3,
            'is_active'   => true,
            'always_visible' => false,
            'content'     => [
                'title'    => 'Nos Services',
                'subtitle' => 'Un guichet complet pour dynamiser l\'écosystème agroécologique à travers une expertise RH de pointe.',
                'cards'    => [
                    ['title' => 'Formation & Orientation',       'description' => 'Programmes de renforcement des capacités techniques et entrepreneuriales adaptés aux réalités du terrain burkinabè.'],
                    ['title' => 'Insertion Professionnelle',     'description' => 'Mise en relation directe entre les diplômés des écoles d\'agronomie et les exploitations agricoles en recherche de main-d\'œuvre qualifiée.', 'stat' => '150+ Jeunes insérés cette année'],
                    ['title' => 'Appui RH aux Entreprises',     'description' => 'Accompagnement dans la structuration de vos équipes et l\'amélioration de vos processus de recrutement.'],
                    ['title' => 'Monitoring & Impact',           'description' => 'Suivi rigoureux de l\'évolution des carrières et analyse de l\'impact social de l\'agroécologie sur le développement local.'],
                ],
            ],
        ],
        [
            'slug'        => 'comment',
            'type'        => 'comment',
            'title'       => 'Comment ça marche ?',
            'order_index' => 4,
            'is_active'   => true,
            'always_visible' => false,
            'content'     => [
                'title' => 'Comment ça marche ?',
                'steps' => [
                    ['title' => 'Accueil & Conseil',   'description' => 'Un premier échange pour comprendre votre profil ou vos besoins de recrutement.'],
                    ['title' => 'Enregistrement',       'description' => 'Inscription sur notre plateforme numérique sécurisée et validation de vos dossiers.'],
                    ['title' => 'Mise en relation',     'description' => 'Matching intelligent entre candidats et entreprises pour des collaborations durables.'],
                ],
            ],
        ],
        [
            'slug'        => 'partenaires',
            'type'        => 'partenaires',
            'title'       => 'Partenaires',
            'order_index' => 5,
            'is_active'   => true,
            'always_visible' => false,
            'content'     => [
                'title' => 'Ils nous font confiance',
                'items' => ['YELEMANI', 'CRIC', 'MAAH', 'BFA-OPS'],
            ],
        ],
        [
            'slug'        => 'contact',
            'type'        => 'contact',
            'title'       => 'Contact',
            'order_index' => 6,
            'is_active'   => true,
            'always_visible' => true,
            'content'     => [
                'title'    => 'Contactez-nous',
                'subtitle' => 'Une question sur nos services ? Notre équipe vous répond sous 48h ouvrées.',
                'address'  => 'Secteur 15, Ouagadougou, Burkina Faso',
                'phone'    => '+226 25 30 00 00',
                'email'    => 'contact@agroecobaara.bf',
                'hours'    => 'Ouvert du Lundi au Vendredi : 08h00 - 16h30',
            ],
        ],
    ];

    public function run(): void
    {
        foreach ($this->sections as $data) {
            LandingSection::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('✅ Sections landing page initialisées.');
    }
}