<?php

namespace Database\Seeders;

use App\Models\LandingSection;
use Illuminate\Database\Seeder;

class LandingSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [

            // ── 1. HERO ───────────────────────────────────────────────────────────
            [
                'slug'           => 'hero',
                'type'           => 'hero',
                'title'          => 'Hero Slider',
                'order_index'    => 1,
                'is_active'      => true,
                'always_visible' => true,
                'content'        => [
                    'slides' => [
                        [
                            'title'               => 'Agro Eco BAARA',
                            'subtitle'            => 'Connecter les talents et les opportunités en Agroécologie',
                            'description'         => 'Bienvenue sur Agro Eco BAARA, la plateforme pour répondre à vos besoins d\'emplois en Agroécologie !',
                            'cta_primary_text'    => 'Nous contacter',
                            'cta_primary_link'    => '#contact',
                            'cta_secondary_text'  => 'En savoir plus',
                            'cta_secondary_link'  => '#audiences',
                            'image_url'           => '/images/medias/1A3A0471.jpg',
                        ],
                        [
                            'title'               => 'Un Guichet pour l\'Emploi',
                            'subtitle'            => 'Accueil, écoute et accompagnement personnalisé',
                            'description'         => 'Un espace dédié pour dynamiser l\'avenir de l\'agriculture durable au Burkina Faso.',
                            'cta_primary_text'    => 'Découvrir le guichet',
                            'cta_primary_link'    => '#guichet',
                            'cta_secondary_text'  => '',
                            'cta_secondary_link'  => '',
                            'image_url'           => '/images/medias/1A3A0497.jpg',
                        ],
                        [
                            'title'               => 'Formation & Insertion',
                            'subtitle'            => 'Renforcer les compétences pour un avenir durable',
                            'description'         => 'Des programmes adaptés pour les jeunes agroécologues et les entreprises du secteur.',
                            'cta_primary_text'    => 'Voir nos services',
                            'cta_primary_link'    => '#audiences',
                            'cta_secondary_text'  => 'Nous contacter',
                            'cta_secondary_link'  => '#contact',
                            'image_url'           => '/images/medias/1A3A0179.jpg',
                        ],
                        [
                            'title'               => 'Pour les Jeunes & Entreprises',
                            'subtitle'            => 'Trouvez vos opportunités, recrutez vos talents',
                            'description'         => 'Jeunes diplômés, ONG, entreprises agroécologiques — la plateforme qui vous met en relation.',
                            'cta_primary_text'    => 'Voir les publics',
                            'cta_primary_link'    => '#audiences',
                            'cta_secondary_text'  => 'Nous contacter',
                            'cta_secondary_link'  => '#contact',
                            'image_url'           => '/images/medias/DSC_0738.JPG',
                        ],
                    ],
                ],
            ],

            // ── 2. LE PROJET ──────────────────────────────────────────────────────
            [
                'slug'           => 'le_projet',
                'type'           => 'le_projet',
                'title'          => 'Le Projet',
                'order_index'    => 2,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'badge'      => 'NOTRE MISSION',
                    'title'      => 'Le Projet',
                    'paragraphs' => [
                        'Le service <strong>Agro Eco BAARA</strong> est une initiative de l\'association <strong>Yelemani</strong> en collaboration avec son partenaire <strong>Centro Regionale d\'Intervento per la Cooperazione (CRIC)</strong>.',
                        'Elle s\'inscrit dans le cadre du projet <em>« Territoires en perspective : transition agroécologique et autonomisation des femmes et des jeunes dans les régions du Kadiogo et de l\'Oubritenga au Burkina Faso »</em>, financé par le <strong>Ministère Italien des Affaires Étrangères et de la Coopération Internationale</strong> (MAECI) à travers l\'<strong>Agence Italienne de Coopération au Développement</strong> (AICS).',
                    ],
                ],
            ],

            // ── 3. S'ADRESSE À VOUS (audiences) ──────────────────────────────────
            [
                'slug'           => 'audiences',
                'type'           => 'audiences',
                'title'          => 'S\'adresse à vous',
                'order_index'    => 3,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'title' => 'Agro Eco BAARA S\'ADRESSE à VOUS',
                    'cards' => [
                        [
                            'key'         => 'jeunes',
                            'icon'        => 'person_search',
                            'title'       => 'Jeunes',
                            'description' => 'Vous êtes en formation, diplômés ou vous souhaitez travailler dans l\'agroécologie ? Trouvez des opportunités et faites évoluer votre parcours.',
                            'cta_text'    => 'Explorer les opportunités',
                        ],
                        [
                            'key'         => 'entreprises',
                            'icon'        => 'corporate_fare',
                            'title'       => 'Entreprises & ONG',
                            'description' => 'Entreprises agroécologiques ou en transition, ONG, organisations paysannes, promoteurs agricoles. Vous recrutez ou vous recherchez des compétences ? Trouvez les profils adaptés.',
                            'cta_text'    => 'Recruter des talents',
                        ],
                        [
                            'key'         => 'centres',
                            'icon'        => 'school',
                            'title'       => 'Centres de formation',
                            'description' => 'Centres professionnels, lycées et centres universitaires actifs en agroécologie. Vous souhaitez accompagner vos étudiants vers l\'emploi ? Facilitez leur insertion.',
                            'cta_text'    => 'Partenariat insertion',
                        ],
                    ],
                ],
            ],

            // ── 4. UN GUICHET POUR L'EMPLOI ───────────────────────────────────────
            [
                'slug'           => 'guichet',
                'type'           => 'guichet',
                'title'          => 'Le Guichet',
                'order_index'    => 4,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'title'           => 'UN GUICHET POUR L\'EMPLOI',
                    'description'     => 'Un espace d\'accueil, d\'écoute et d\'accompagnement personnalisé pour dynamiser l\'avenir de l\'agriculture durable au Burkina Faso.',
                    'localisation'    => '1200 Logements/Ouagadougou, Burkina Faso',
                    'horaires'        => 'Lundi – Vendredi, 8h00 – 17h00',
                    'contacts'        => 'Tél: [Numéro de téléphone] | Email: [Adresse email]',
                    'image_url'       => '/images/medias/1A3A0497.jpg',
                    'image_caption'   => '"Une équipe dédiée pour orienter vos projets professionnels."',
                ],
            ],

            // ── 5. CE QUE VOUS POUVEZ TROUVER ────────────────────────────────────
            [
                'slug'           => 'ce_que_vous_pouvez_trouver',
                'type'           => 'ce_que_vous_pouvez_trouver',
                'title'          => 'Ce que vous pouvez trouver',
                'order_index'    => 5,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'title'   => 'CE QUE VOUS POUVEZ TROUVER',
                    'columns' => [
                        [
                            'key'   => 'jeunes',
                            'icon'  => 'school',
                            'color' => 'primary',
                            'title' => 'Pour les jeunes',
                            'items' => [
                                'Conseils emploi & orientation',
                                'Accompagnement CV / Profil professionnel',
                                'Offres (emplois, stages, opportunités)',
                                'Informations sur les formations & financements',
                                'Accompagnement au lancement d\'activité',
                            ],
                        ],
                        [
                            'key'   => 'entreprises',
                            'icon'  => 'corporate_fare',
                            'color' => 'secondary',
                            'title' => 'Pour les entreprises',
                            'items' => [
                                'Appui au recrutement spécialisé',
                                'Accès aux profils de jeunes formés',
                                'Accompagnement à la définition des besoins',
                                'Mise en relation directe avec les talents',
                            ],
                        ],
                    ],
                ],
            ],

            // ── 6. COMMENT ÇA MARCHE ──────────────────────────────────────────────
            [
                'slug'           => 'comment',
                'type'           => 'comment',
                'title'          => 'Comment ça marche ?',
                'order_index'    => 6,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'title' => 'COMMENT ÇA MARCHE ?',
                    'steps' => [
                        ['number' => '1', 'title' => 'Premier Contact',  'description' => 'Vous venez au guichet ou vous nous contactez par téléphone ou par email'],
                        ['number' => '2', 'title' => 'Diagnostic',       'description' => 'Un opérateur vous accompagne pour clarifier votre besoin ou votre profil'],
                        ['number' => '3', 'title' => 'Mise en relation', 'description' => 'Nous facilitons la mise en relation et assurons un suivi rigoureux'],
                    ],
                ],
            ],

            // ── 7. AUTRES SERVICES ────────────────────────────────────────────────
            [
                'slug'           => 'autres_services',
                'type'           => 'autres_services',
                'title'          => 'Autres Services',
                'order_index'    => 7,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'title'        => 'AUTRES SERVICES',
                    'description'  => 'Nous créons des espaces d\'échange pour renforcer la communauté agroécologique :',
                    'facebook_link'=> 'https://facebook.com/yelemani.burkina',
                    'facebook_text'=> 'Suivez-nous sur Facebook',
                    'services'     => [
                        ['icon' => 'groups',           'label' => 'Ateliers thématiques'],
                        ['icon' => 'campaign',         'label' => 'Journées de sensibilisation'],
                        ['icon' => 'diversity_3',      'label' => 'Moments de rencontre'],
                        ['icon' => 'forum',            'label' => 'Échanges d\'expériences'],
                        ['icon' => 'hub',              'label' => 'Réseautage'],
                    ],
                ],
            ],

            // ── 8. PARTENAIRES ────────────────────────────────────────────────────
            [
                'slug'           => 'partenaires',
                'type'           => 'partenaires',
                'title'          => 'Partenaires',
                'order_index'    => 8,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'title' => 'NOS PARTENAIRES',
                    'items' => [
                        ['name' => 'Commune de Loumbila',     'logo' => '/images/partners/Image1.png', 'description' => 'Collectivité territoriale partenaire du projet.',                     'website' => '#',                         'social_label' => 'Site Web',  'social_icon' => 'language'],
                        ['name' => 'Yelemani',                'logo' => '/images/partners/Image2.png', 'description' => 'Organisation porteuse dédiée à la souveraineté alimentaire.',        'website' => 'https://facebook.com/yelemani.burkina', 'social_label' => 'Facebook', 'social_icon' => 'facebook'],
                        ['name' => 'CRIC',                    'logo' => '/images/partners/Image3.png', 'description' => 'Centro Regionale d\'Intervento per la Cooperazione.',                'website' => '#',                         'social_label' => 'Site Web',  'social_icon' => 'language'],
                        ['name' => 'Ministero dell\'Interno', 'logo' => '/images/partners/Image4.png', 'description' => 'Partenaire institutionnel — Coopération italienne.',                  'website' => '#',                         'social_label' => 'Site Web',  'social_icon' => 'language'],
                        ['name' => 'HUMUS JOB',               'logo' => '/images/partners/Image5.jpg', 'description' => 'Spécialiste de l\'insertion professionnelle verte.',                 'website' => '#',                         'social_label' => 'Site Web',  'social_icon' => 'language'],
                        ['name' => 'CNABio',                  'logo' => '/images/partners/Image6.jpg', 'description' => 'Conseil National de l\'Agriculture Biologique du Burkina Faso.',     'website' => 'https://www.cnabio.net',     'social_label' => 'Site Web',  'social_icon' => 'language'],
                        ['name' => 'Youth Connect BF',        'logo' => '/images/partners/Image7.png', 'description' => 'Réseau pour l\'épanouissement et l\'emploi des jeunes.',             'website' => '#',                         'social_label' => 'Site Web',  'social_icon' => 'language'],
                        ['name' => 'Agrinovia',               'logo' => '/images/partners/Image8.jpg', 'description' => 'Partenaire en innovation et développement agroécologique.',          'website' => '#',                         'social_label' => 'Site Web',  'social_icon' => 'language'],
                    ],
                ],
            ],

            // ── 9. TÉMOIGNAGES ────────────────────────────────────────────────────
            [
                'slug'           => 'temoignages',
                'type'           => 'temoignages',
                'title'          => 'Témoignages',
                'order_index'    => 9,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'title' => 'TÉMOIGNAGES',
                    'items' => [
                        ['name' => 'Issa Traoré',            'role' => 'Stagiaire Agroécologue',       'avatar_color' => 'primary',   'text' => 'Grâce au guichet, j\'ai trouvé un stage en maraîchage bio qui a changé ma vision de l\'agriculture. L\'accompagnement était top !'],
                        ['name' => 'Fatimata Ouédraogo',     'role' => 'Gérante de ferme bio',         'avatar_color' => 'secondary', 'text' => 'Le guichet m\'a aidée à structurer mon projet de ferme pédagogique et à trouver les bons partenaires locaux.'],
                        ['name' => 'Adama Barry',            'role' => 'Jeune Diplômé',                'avatar_color' => 'tertiary',  'text' => 'Un service indispensable pour les jeunes qui veulent s\'investir dans le durable au Burkina.'],
                    ],
                ],
            ],

            // ── 10. MÉDIATHÈQUE ──────────────────────────────────────────────────
            [
                'slug'           => 'mediatheque',
                'type'           => 'mediatheque',
                'title'          => 'Médiathèque',
                'order_index'    => 10,
                'is_active'      => true,
                'always_visible' => false,
                'content'        => [
                    'title'       => 'MÉDIATHÈQUE',
                    'description' => 'Découvrez nos activités à travers les photos du terrain.',
                    'photos'      => [
                        ['src' => '/images/medias/_DSC3855.jpg',  'alt' => 'Terrain agroécologie', 'category' => 'terrain'],
                        ['src' => '/images/medias/1A3A0174.jpg',  'alt' => 'Terrain',              'category' => 'terrain'],
                        ['src' => '/images/medias/1A3A0179.jpg',  'alt' => 'Formation',            'category' => 'formation'],
                        ['src' => '/images/medias/1A3A0181.jpg',  'alt' => 'Formation',            'category' => 'formation'],
                        ['src' => '/images/medias/1A3A0185.jpg',  'alt' => 'Formation',            'category' => 'formation'],
                        ['src' => '/images/medias/1A3A0471.jpg',  'alt' => 'Terrain',              'category' => 'terrain'],
                        ['src' => '/images/medias/1A3A0497.jpg',  'alt' => 'Événement',            'category' => 'evenement'],
                        ['src' => '/images/medias/1A3A0515.jpg',  'alt' => 'Formation',            'category' => 'formation'],
                        ['src' => '/images/medias/1A3A0532.jpg',  'alt' => 'Événement',            'category' => 'evenement'],
                        ['src' => '/images/medias/DSC_0697.JPG',  'alt' => 'Événement',            'category' => 'evenement'],
                        ['src' => '/images/medias/DSC_0738.JPG',  'alt' => 'Terrain',              'category' => 'terrain'],
                        ['src' => '/images/medias/DSC_0742.JPG',  'alt' => 'Événement',            'category' => 'evenement'],
                    ],
                ],
            ],

            // ── 11. CONTACT ───────────────────────────────────────────────────────
            [
                'slug'           => 'contact',
                'type'           => 'contact',
                'title'          => 'Contact',
                'order_index'    => 11,
                'is_active'      => true,
                'always_visible' => true,
                'content'        => [
                    'title'    => 'Contactez-nous',
                    'subtitle' => 'Une question sur nos services ? Notre équipe vous répond sous 48h ouvrées.',
                    'address'  => '1200 Logements, Ouagadougou, Burkina Faso',
                    'phone'    => '[Numéro de téléphone]',
                    'email'    => '[Adresse email]',
                    'hours'    => 'Ouvert du Lundi au Vendredi : 08h00 – 17h00',
                ],
            ],

        ];

        foreach ($sections as $data) {
            LandingSection::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('✅ ' . count($sections) . ' sections landing page initialisées/mises à jour.');
    }
}
