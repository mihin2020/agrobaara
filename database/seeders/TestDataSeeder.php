<?php

namespace Database\Seeders;

use App\Enums\CompanyStatus;
use App\Enums\ContractType;
use App\Enums\EducationLevel;
use App\Enums\Gender;
use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\Company;
use App\Models\CompanySite;
use App\Models\JobOffer;
use App\Models\ReferentialCommune;
use App\Models\ReferentialLanguage;
use App\Models\ReferentialSkill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Référentiels ───────────────────────────────────────────────────────
        $communes = ReferentialCommune::all()->keyBy('name');
        $skills   = ReferentialSkill::all()->keyBy('name');
        $langs    = ReferentialLanguage::all()->keyBy('code');

        $ouaga   = $communes->get('Ouagadougou');
        $bobo    = $communes->get('Bobo-Dioulasso');
        $banfora = $communes->get('Banfora');
        $koudou  = $communes->get('Koudougou');

        // ── Utilisateurs de test ───────────────────────────────────────────────
        $moderateur = User::firstOrCreate(
            ['email' => 'moderateur@agroecobaara.bf'],
            [
                'id'         => Str::uuid()->toString(),
                'first_name' => 'Ibrahim',
                'last_name'  => 'OUEDRAOGO',
                'password'   => Hash::make('Moderateur@2027!'),
                'status'     => UserStatus::Active,
            ]
        );
        $moderateur->assignRole(UserRole::Moderateur->value);

        $operateur1 = User::firstOrCreate(
            ['email' => 'operateur1@agroecobaara.bf'],
            [
                'id'         => Str::uuid()->toString(),
                'first_name' => 'Fatoumata',
                'last_name'  => 'SAWADOGO',
                'password'   => Hash::make('Operateur@2027!'),
                'status'     => UserStatus::Active,
            ]
        );
        $operateur1->assignRole(UserRole::Operateur->value);

        $operateur2 = User::firstOrCreate(
            ['email' => 'operateur2@agroecobaara.bf'],
            [
                'id'         => Str::uuid()->toString(),
                'first_name' => 'Salif',
                'last_name'  => 'KONATE',
                'password'   => Hash::make('Operateur@2027!'),
                'status'     => UserStatus::Active,
            ]
        );
        $operateur2->assignRole(UserRole::Operateur->value);

        // ── Entreprises ────────────────────────────────────────────────────────
        $companiesData = [
            [
                'reference'             => 'ENT-2026-001',
                'name'                  => 'Ferme Verte Barka SARL',
                'status'                => CompanyStatus::SARL,
                'legal_rep_first_name'  => 'Adama',
                'legal_rep_last_name'   => 'BARKA',
                'activity_types'        => ['maraichage', 'transformation'],
                'description'           => 'Exploitation maraîchère spécialisée dans la production de légumes biologiques pour les marchés locaux de Ouagadougou.',
                'phone'                 => '+226 70 11 22 33',
                'email'                 => 'contact@ferme-barka.bf',
                'need_training'         => true,
                'need_financing'        => false,
                'need_contract_support' => true,
                'created_by'            => $operateur1->id,
                'site_label'            => 'Site principal — Tanghin',
                'site_commune'          => $ouaga,
            ],
            [
                'reference'             => 'ENT-2026-002',
                'name'                  => 'Coopérative Agro-Hauts Bassins',
                'status'                => CompanyStatus::SCOOP,
                'legal_rep_first_name'  => 'Mariam',
                'legal_rep_last_name'   => 'COULIBALY',
                'activity_types'        => ['elevage', 'agroforesterie'],
                'description'           => 'Coopérative regroupant 45 agriculteurs de la région des Hauts-Bassins, spécialisée en élevage et agroforesterie.',
                'phone'                 => '+226 76 44 55 66',
                'email'                 => 'coop.hautsbassins@gmail.com',
                'need_training'         => false,
                'need_financing'        => true,
                'need_contract_support' => false,
                'created_by'            => $operateur1->id,
                'site_label'            => 'Siège Bobo-Dioulasso',
                'site_commune'          => $bobo,
            ],
            [
                'reference'             => 'ENT-2026-003',
                'name'                  => 'AGRI-INNOV Burkina',
                'status'                => CompanyStatus::SAS,
                'legal_rep_first_name'  => 'Seydou',
                'legal_rep_last_name'   => 'TRAORE',
                'activity_types'        => ['conseil', 'formation'],
                'description'           => 'Entreprise de conseil et de formation agricole, production de semences améliorées et accompagnement des producteurs.',
                'phone'                 => '+226 65 77 88 99',
                'email'                 => 'info@agri-innov.bf',
                'need_training'         => false,
                'need_financing'        => false,
                'need_contract_support' => false,
                'created_by'            => $operateur2->id,
                'site_label'            => 'Centre de formation Ouaga',
                'site_commune'          => $ouaga,
            ],
            [
                'reference'             => 'ENT-2026-004',
                'name'                  => 'Ferme Cascades Bio',
                'status'                => CompanyStatus::EntrepriseIndividuelle,
                'legal_rep_first_name'  => 'Romuald',
                'legal_rep_last_name'   => 'SOME',
                'activity_types'        => ['transformation', 'maraichage'],
                'description'           => 'Ferme bio dans la région des Cascades, production de fruits tropicaux et transformation artisanale locale.',
                'phone'                 => '+226 61 23 45 67',
                'email'                 => 'cascadesbio@yahoo.fr',
                'need_training'         => true,
                'need_financing'        => true,
                'need_contract_support' => false,
                'created_by'            => $operateur2->id,
                'site_label'            => 'Plantation Banfora',
                'site_commune'          => $banfora,
            ],
        ];

        $createdCompanies = [];
        foreach ($companiesData as $data) {
            $company = Company::firstOrCreate(
                ['reference' => $data['reference']],
                collect($data)->except(['site_label', 'site_commune'])->toArray()
            );
            CompanySite::firstOrCreate(
                ['company_id' => $company->id, 'label' => $data['site_label']],
                [
                    'commune_id' => $data['site_commune']?->id,
                    'is_main'    => true,
                ]
            );
            $createdCompanies[$data['reference']] = $company;
        }

        // ── Offres d'emploi ────────────────────────────────────────────────────
        $offersData = [
            [
                'reference'           => 'OFF-2026-001',
                'company_id'          => $createdCompanies['ENT-2026-001']->id,
                'title'               => 'Technicien maraîcher spécialisé bio',
                'contract_type'       => ContractType::Salarie,
                'duration'            => '12 mois renouvelables',
                'positions_count'     => 2,
                'status'              => OfferStatus::Publiee,
                'published_at'        => now()->subDays(5),
                'mission_description' => 'Gestion des parcelles maraîchères, application des techniques biologiques, suivi des cultures et récoltes.',
                'economic_conditions' => 'SMAG + primes de rendement trimestrielles',
                'locations'           => [['commune' => 'Ouagadougou', 'label' => 'Ouagadougou — Tanghin']],
                'start_date'          => now()->addDays(30)->toDateString(),
                'created_by'          => $operateur1->id,
                'published_by'        => $operateur1->id,
                'skills'              => ['Maraîchage', 'Agriculture biologique', 'Irrigation'],
            ],
            [
                'reference'           => 'OFF-2026-002',
                'company_id'          => $createdCompanies['ENT-2026-002']->id,
                'title'               => 'Responsable élevage bovin',
                'contract_type'       => ContractType::Salarie,
                'duration'            => '24 mois',
                'positions_count'     => 1,
                'status'              => OfferStatus::Publiee,
                'published_at'        => now()->subDays(10),
                'mission_description' => 'Encadrement d\'un troupeau de 80 bovins. Suivi sanitaire, gestion du pâturage et coordination des éleveurs membres.',
                'economic_conditions' => 'Salaire fixe + logement sur site',
                'locations'           => [['commune' => 'Bobo-Dioulasso', 'label' => 'Bobo-Dioulasso']],
                'start_date'          => now()->addDays(45)->toDateString(),
                'created_by'          => $operateur1->id,
                'published_by'        => $operateur1->id,
                'skills'              => ['Élevage bovin', 'Gestion d\'exploitation'],
            ],
            [
                'reference'           => 'OFF-2026-003',
                'company_id'          => $createdCompanies['ENT-2026-003']->id,
                'title'               => 'Formateur en agroforesterie',
                'contract_type'       => ContractType::Ponctuel,
                'duration'            => '3 mois',
                'positions_count'     => 1,
                'status'              => OfferStatus::Publiee,
                'published_at'        => now()->subDays(3),
                'mission_description' => 'Animation de sessions de formation sur les techniques d\'agroforesterie pour des groupements de producteurs dans 3 provinces.',
                'economic_conditions' => 'Per diem journalier + transport pris en charge',
                'locations'           => [['commune' => 'Ouagadougou', 'label' => 'Ouagadougou + déplacements province']],
                'start_date'          => now()->addDays(15)->toDateString(),
                'created_by'          => $operateur2->id,
                'published_by'        => $operateur2->id,
                'skills'              => ['Agroforesterie', 'Gestion de la fertilité des sols'],
            ],
            [
                'reference'           => 'OFF-2026-004',
                'company_id'          => $createdCompanies['ENT-2026-004']->id,
                'title'               => 'Ouvrier polyvalent — plantation Banfora',
                'contract_type'       => ContractType::Saisonnier,
                'duration'            => '6 mois',
                'positions_count'     => 4,
                'status'              => OfferStatus::Brouillon,
                'mission_description' => 'Travaux agricoles saisonniers : récolte des fruits tropicaux, conditionnement, entretien des parcelles bio.',
                'economic_conditions' => 'SMAG saisonnier + hébergement sur site',
                'locations'           => [['commune' => 'Banfora', 'label' => 'Banfora — Région des Cascades']],
                'start_date'          => now()->addDays(60)->toDateString(),
                'created_by'          => $operateur2->id,
                'skills'              => ['Arboriculture fruitière', 'Transformation agro-alimentaire'],
            ],
            [
                'reference'           => 'OFF-2026-005',
                'company_id'          => $createdCompanies['ENT-2026-001']->id,
                'title'               => 'Responsable commercialisation produits bio',
                'contract_type'       => ContractType::Salarie,
                'duration'            => '12 mois',
                'positions_count'     => 1,
                'status'              => OfferStatus::Archivee,
                'archived_at'         => now()->subDays(2),
                'mission_description' => 'Développement des circuits de commercialisation, prospection hôtels/supermarchés, suivi des livraisons.',
                'economic_conditions' => 'Fixe + commission sur chiffre d\'affaires',
                'locations'           => [['commune' => 'Ouagadougou', 'label' => 'Ouagadougou']],
                'start_date'          => now()->subDays(90)->toDateString(),
                'created_by'          => $operateur1->id,
                'skills'              => ['Marketing agricole', 'Gestion d\'exploitation'],
            ],
        ];

        $createdOffers = [];
        foreach ($offersData as $data) {
            $offer = JobOffer::firstOrCreate(
                ['reference' => $data['reference']],
                collect($data)->except(['skills'])->toArray()
            );
            $skillIds = collect($data['skills'])
                ->map(fn($n) => $skills->get($n)?->id)
                ->filter()->values()->toArray();
            if (!empty($skillIds)) {
                $offer->skills()->syncWithoutDetaching($skillIds);
            }
            $createdOffers[$data['reference']] = $offer;
        }

        // ── Candidats ──────────────────────────────────────────────────────────
        $candidatesData = [
            [
                'reference'          => 'CAN-2026-001',
                'first_name'         => 'Boureima',
                'last_name'          => 'ZONGO',
                'gender'             => Gender::Male,
                'birth_date'         => '1998-04-15',
                'birth_place'        => 'Ouagadougou',
                'nationality'        => 'Burkinabè',
                'commune_id'         => $ouaga?->id,
                'phone'              => '+226 70 12 34 56',
                'email'              => 'b.zongo@gmail.com',
                'education_level'    => EducationLevel::Secondaire,
                'agro_training_text' => 'BTS Agronomie — Institut Supérieur de Technologie du Burkina (IST-B)',
                'has_previous_jobs'  => true,
                'need_types'         => ['emploi_salarie'],
                'need_training'      => false,
                'need_financing'     => false,
                'need_cv_support'    => false,
                'created_by'         => $operateur1->id,
                'skills'             => ['Maraîchage', 'Agriculture biologique', 'Irrigation'],
                'languages'          => ['fr', 'mos'],
            ],
            [
                'reference'          => 'CAN-2026-002',
                'first_name'         => 'Aïssata',
                'last_name'          => 'OUEDRAOGO',
                'gender'             => Gender::Female,
                'birth_date'         => '2000-09-22',
                'birth_place'        => 'Bobo-Dioulasso',
                'nationality'        => 'Burkinabè',
                'commune_id'         => $bobo?->id,
                'phone'              => '+226 65 98 76 54',
                'email'              => 'a.ouedraogo@yahoo.fr',
                'education_level'    => EducationLevel::Secondaire,
                'agro_training_text' => 'Lycée Agricole de Matourkou — Filière élevage et agroforesterie',
                'has_previous_jobs'  => false,
                'need_types'         => ['emploi_salarie', 'formation'],
                'need_training'      => true,
                'need_financing'     => false,
                'need_cv_support'    => true,
                'created_by'         => $operateur1->id,
                'skills'             => ['Élevage bovin', 'Agroforesterie', 'Élevage ovin/caprin'],
                'languages'          => ['fr', 'dyu', 'mos'],
            ],
            [
                'reference'          => 'CAN-2026-003',
                'first_name'         => 'Wendlassida',
                'last_name'          => 'KABORE',
                'gender'             => Gender::Male,
                'birth_date'         => '1995-02-10',
                'birth_place'        => 'Koudougou',
                'nationality'        => 'Burkinabè',
                'commune_id'         => $koudou?->id,
                'phone'              => '+226 77 22 33 44',
                'education_level'    => EducationLevel::Licence,
                'agro_training_text' => 'Licence en Sciences Agronomiques — Université Nazi Boni de Bobo-Dioulasso',
                'has_previous_jobs'  => true,
                'need_types'         => ['emploi_salarie', 'entrepreneuriat'],
                'need_training'      => false,
                'need_financing'     => true,
                'need_cv_support'    => false,
                'created_by'         => $operateur2->id,
                'skills'             => ['Agroforesterie', 'Gestion d\'exploitation', 'Marketing agricole', 'Comptabilité agricole'],
                'languages'          => ['fr', 'mos', 'en'],
            ],
            [
                'reference'          => 'CAN-2026-004',
                'first_name'         => 'Salamata',
                'last_name'          => 'TRAORE',
                'gender'             => Gender::Female,
                'birth_date'         => '2001-07-05',
                'birth_place'        => 'Banfora',
                'nationality'        => 'Burkinabè',
                'commune_id'         => $banfora?->id,
                'phone'              => '+226 61 55 66 77',
                'education_level'    => EducationLevel::Secondaire,
                'agro_training_text' => 'Formation pratique — Centre Agro-Écologique de Banfora, 2022',
                'has_previous_jobs'  => true,
                'need_types'         => ['emploi_saisonnier'],
                'need_training'      => true,
                'need_financing'     => false,
                'need_cv_support'    => true,
                'created_by'         => $operateur2->id,
                'skills'             => ['Arboriculture fruitière', 'Transformation agro-alimentaire', 'Conservation des aliments'],
                'languages'          => ['fr', 'dyu'],
            ],
            [
                'reference'          => 'CAN-2026-005',
                'first_name'         => 'Dramane',
                'last_name'          => 'BELEM',
                'gender'             => Gender::Male,
                'birth_date'         => '1996-11-30',
                'birth_place'        => 'Ouagadougou',
                'nationality'        => 'Burkinabè',
                'commune_id'         => $ouaga?->id,
                'phone'              => '+226 70 44 55 88',
                'email'              => 'd.belem@gmail.com',
                'education_level'    => EducationLevel::Secondaire,
                'agro_training_text' => 'BTS Production Végétale — INERA Ouagadougou',
                'has_previous_jobs'  => true,
                'need_types'         => ['emploi_salarie'],
                'need_training'      => false,
                'need_financing'     => false,
                'need_cv_support'    => false,
                'created_by'         => $operateur1->id,
                'skills'             => ['Maraîchage', 'Pépinière', 'Irrigation', 'Bio-compostage'],
                'languages'          => ['fr', 'mos', 'dyu'],
            ],
            [
                'reference'          => 'CAN-2026-006',
                'first_name'         => 'Honorine',
                'last_name'          => 'DABIRE',
                'gender'             => Gender::Female,
                'birth_date'         => '1999-03-18',
                'birth_place'        => 'Gaoua',
                'nationality'        => 'Burkinabè',
                'commune_id'         => $ouaga?->id,
                'phone'              => '+226 76 33 22 11',
                'education_level'    => EducationLevel::Licence,
                'agro_training_text' => 'Licence Environnement et Développement Durable — Université Thomas Sankara',
                'has_previous_jobs'  => false,
                'need_types'         => ['formation', 'emploi_salarie'],
                'need_training'      => true,
                'need_financing'     => true,
                'need_cv_support'    => true,
                'created_by'         => $operateur2->id,
                'skills'             => ['Agroforesterie', 'Gestion de l\'eau', 'Énergies renouvelables', 'Gestion de la fertilité des sols'],
                'languages'          => ['fr', 'en'],
            ],
            [
                'reference'          => 'CAN-2026-007',
                'first_name'         => 'Issa',
                'last_name'          => 'SIMPORE',
                'gender'             => Gender::Male,
                'birth_date'         => '1993-08-25',
                'birth_place'        => 'Ouahigouya',
                'nationality'        => 'Burkinabè',
                'commune_id'         => $ouaga?->id,
                'phone'              => '+226 65 11 99 88',
                'education_level'    => EducationLevel::Master,
                'agro_training_text' => 'Master Agronomie Tropicale — Institut du Développement Rural (IDR), Bobo-Dioulasso',
                'has_previous_jobs'  => true,
                'need_types'         => ['emploi_salarie'],
                'need_training'      => false,
                'need_financing'     => false,
                'need_cv_support'    => false,
                'operator_notes'     => 'Profil senior très qualifié — prioriser pour postes d\'encadrement et de direction.',
                'created_by'         => $operateur1->id,
                'skills'             => ['Élevage bovin', 'Gestion d\'exploitation', 'Comptabilité agricole', 'Agroforesterie', 'Mécanisation agricole'],
                'languages'          => ['fr', 'mos', 'en'],
            ],
            [
                'reference'          => 'CAN-2026-008',
                'first_name'         => 'Rasmané',
                'last_name'          => 'YAGO',
                'gender'             => Gender::Male,
                'birth_date'         => '2002-05-12',
                'birth_place'        => 'Ziniaré',
                'nationality'        => 'Burkinabè',
                'commune_id'         => $ouaga?->id,
                'phone'              => '+226 71 66 77 55',
                'education_level'    => EducationLevel::Secondaire,
                'agro_training_text' => 'Baccalauréat Série D — Formation Professionnelle Agriculture Durable',
                'has_previous_jobs'  => false,
                'need_types'         => ['apprentissage', 'emploi_salarie'],
                'need_training'      => true,
                'need_financing'     => false,
                'need_cv_support'    => true,
                'created_by'         => $operateur2->id,
                'skills'             => ['Maraîchage', 'Bio-pesticides', 'Bio-compostage'],
                'languages'          => ['fr', 'mos'],
            ],
        ];

        $createdCandidates = [];
        foreach ($candidatesData as $data) {
            $candidate = Candidate::firstOrCreate(
                ['reference' => $data['reference']],
                collect($data)->except(['skills', 'languages'])->toArray()
            );
            $skillIds = collect($data['skills'])
                ->map(fn($n) => $skills->get($n)?->id)
                ->filter()->values()->toArray();
            if (!empty($skillIds)) {
                $candidate->skills()->syncWithoutDetaching($skillIds);
            }
            $langIds = collect($data['languages'])
                ->map(fn($code) => $langs->get($code)?->id)
                ->filter()->values()->toArray();
            if (!empty($langIds)) {
                $candidate->languages()->syncWithoutDetaching($langIds);
            }
            $createdCandidates[$data['reference']] = $candidate;
        }

        // ── Matchings ──────────────────────────────────────────────────────────
        $matchings = [
            [
                'candidate_id' => $createdCandidates['CAN-2026-001']->id,
                'offer_id'     => $createdOffers['OFF-2026-001']->id,
                'status'       => MatchStatus::Entretien,
                'operator_id'  => $operateur1->id,
                'score'        => 88.5,
                'notes'        => 'Candidat très motivé. Entretien prévu le 20/05/2026.',
                'proposed_at'  => now()->subDays(8),
            ],
            [
                'candidate_id' => $createdCandidates['CAN-2026-005']->id,
                'offer_id'     => $createdOffers['OFF-2026-001']->id,
                'status'       => MatchStatus::Contactee,
                'operator_id'  => $operateur1->id,
                'score'        => 82.0,
                'notes'        => 'Contacté par téléphone, attend confirmation pour l\'entretien.',
                'proposed_at'  => now()->subDays(5),
            ],
            [
                'candidate_id' => $createdCandidates['CAN-2026-002']->id,
                'offer_id'     => $createdOffers['OFF-2026-002']->id,
                'status'       => MatchStatus::Proposee,
                'operator_id'  => $operateur1->id,
                'score'        => 75.5,
                'notes'        => 'Profil correspond bien. Proposer en priorité.',
                'proposed_at'  => now()->subDays(3),
            ],
            [
                'candidate_id' => $createdCandidates['CAN-2026-007']->id,
                'offer_id'     => $createdOffers['OFF-2026-002']->id,
                'status'       => MatchStatus::Acceptee,
                'operator_id'  => $operateur2->id,
                'score'        => 95.0,
                'notes'        => 'Profil senior idéal. Contrat signé le 10/05/2026.',
                'proposed_at'  => now()->subDays(15),
                'closed_at'    => now()->subDays(4),
            ],
            [
                'candidate_id' => $createdCandidates['CAN-2026-003']->id,
                'offer_id'     => $createdOffers['OFF-2026-003']->id,
                'status'       => MatchStatus::Contactee,
                'operator_id'  => $operateur2->id,
                'score'        => 79.0,
                'notes'        => 'Compétences en agroforesterie confirmées. Disponible sous 15 jours.',
                'proposed_at'  => now()->subDays(2),
            ],
            [
                'candidate_id' => $createdCandidates['CAN-2026-004']->id,
                'offer_id'     => $createdOffers['OFF-2026-004']->id,
                'status'       => MatchStatus::Proposee,
                'operator_id'  => $operateur2->id,
                'score'        => 71.0,
                'notes'        => 'Correspondance géographique parfaite (Banfora).',
                'proposed_at'  => now()->subDays(1),
            ],
            [
                'candidate_id' => $createdCandidates['CAN-2026-001']->id,
                'offer_id'     => $createdOffers['OFF-2026-005']->id,
                'status'       => MatchStatus::Refusee,
                'operator_id'  => $operateur1->id,
                'score'        => 65.0,
                'notes'        => 'Offre archivée. Candidat a refusé les conditions de déplacement.',
                'proposed_at'  => now()->subDays(20),
                'closed_at'    => now()->subDays(7),
            ],
        ];

        foreach ($matchings as $data) {
            CandidateMatch::firstOrCreate(
                ['candidate_id' => $data['candidate_id'], 'offer_id' => $data['offer_id']],
                $data
            );
        }

        $this->command->info('✅ Données de test insérées :');
        $this->command->line('   • 3 utilisateurs  : modérateur + 2 opérateurs');
        $this->command->line('   • 4 entreprises   + 4 sites');
        $this->command->line('   • 5 offres        (3 publiées, 1 brouillon, 1 archivée)');
        $this->command->line('   • 8 candidats     avec compétences et langues');
        $this->command->line('   • 7 mises en relation (proposée/contactée/entretien/acceptée/refusée)');
        $this->command->newLine();
        $this->command->line('   Comptes : moderateur@agroecobaara.bf / Moderateur@2027!');
        $this->command->line('             operateur1@agroecobaara.bf / Operateur@2027!');
        $this->command->line('             operateur2@agroecobaara.bf / Operateur@2027!');
    }
}