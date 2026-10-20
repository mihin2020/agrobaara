<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolesPermissionsSeeder extends Seeder
{
    private array $permissions = [
        // Candidats
        'candidates' => [
            'candidates.view'                => 'Voir les candidats',
            'candidates.create'              => 'Créer un candidat',
            'candidates.update'              => 'Modifier un candidat',
            'candidates.delete'              => 'Supprimer un candidat',
            'candidates.export'              => 'Exporter les candidats',
            'candidates.view_internal_needs' => 'Voir les besoins internes',
        ],
        // Entreprises
        'companies' => [
            'companies.view'   => 'Voir les entreprises',
            'companies.create' => 'Créer une entreprise',
            'companies.update' => 'Modifier une entreprise',
            'companies.delete' => 'Supprimer une entreprise',
            'companies.export' => 'Exporter les entreprises',
        ],
        // Offres
        'offers' => [
            'offers.view'     => 'Voir les offres',
            'offers.create'   => 'Créer une offre',
            'offers.update'   => 'Modifier une offre',
            'offers.delete'   => 'Supprimer une offre',
            'offers.publish'  => 'Publier une offre',
            'offers.archive'  => 'Archiver une offre',
            'offers.validate' => 'Valider une offre portail',
        ],
        // Phase 2
        'registrations' => [
            'registrations.view'    => 'Voir les demandes d\'inscription',
            'registrations.approve' => 'Approuver une inscription',
            'registrations.reject'  => 'Refuser une inscription',
        ],
        'testimonials' => [
            'testimonials.view'       => 'Voir les témoignages',
            'testimonials.approve'    => 'Valider un témoignage',
            'testimonials.unpublish'  => 'Dépublier un témoignage',
        ],
        'news' => [
            'news.view'    => 'Voir les actualités',
            'news.create'  => 'Créer une actualité',
            'news.update'  => 'Modifier une actualité',
            'news.publish' => 'Publier une actualité',
        ],
        // Matching
        'matches' => [
            'matches.view'   => 'Voir les mises en relation',
            'matches.create' => 'Créer une mise en relation',
            'matches.update' => 'Modifier une mise en relation',
            'matches.close'  => 'Clôturer une mise en relation',
        ],
        // Contrats
        'contracts' => [
            'contracts.view'   => 'Voir les contrats',
            'contracts.create' => 'Créer un contrat',
            'contracts.update' => 'Modifier un contrat',
        ],
        // Utilisateurs / Admin
        'users' => [
            'users.view'        => 'Voir les utilisateurs',
            'users.create'      => 'Créer un utilisateur',
            'users.update'      => 'Modifier un utilisateur',
            'users.delete'      => 'Supprimer un utilisateur',
            'users.assign_role' => 'Assigner un rôle',
        ],
        // Rôles et permissions
        'roles' => [
            'roles.manage'       => 'Gérer les rôles',
            'permissions.manage' => 'Gérer les permissions',
        ],
        // Notifications email
        'notifications' => [
            'notifications.contact'    => 'Recevoir les notifications de contact',
            'notifications.candidates' => 'Recevoir les notifications candidats',
            'notifications.companies'  => 'Recevoir les notifications entreprises',
            'notifications.offers'     => 'Recevoir les notifications offres',
            'notifications.matches'    => 'Recevoir les notifications matching',
        ],
        // Administration
        'admin' => [
            'audit.view'          => "Consulter le journal d'audit",
            'settings.view'       => 'Voir les paramètres',
            'settings.update'     => 'Modifier les paramètres',
            'referentials.manage' => 'Gérer les référentiels',
            'landing.configure'   => 'Configurer la landing page',
            'landing.preview'     => 'Prévisualiser la landing page',
            'statistics.global'   => 'Voir les statistiques globales',
            'statistics.team'     => "Voir les statistiques d'équipe",
            'statistics.personal' => 'Voir ses statistiques personnelles',
        ],
    ];

    private array $rolePermissions = [
        UserRole::SuperAdmin->value => '*', // Toutes les permissions
        UserRole::Moderateur->value => [
            'candidates.view', 'candidates.create', 'candidates.update', 'candidates.delete',
            'candidates.export', 'candidates.view_internal_needs',
            'companies.view', 'companies.create', 'companies.update', 'companies.delete', 'companies.export',
            'offers.view', 'offers.create', 'offers.update', 'offers.delete', 'offers.publish', 'offers.archive', 'offers.validate',
            'matches.view', 'matches.create', 'matches.update', 'matches.close',
            'contracts.view', 'contracts.create', 'contracts.update',
            'registrations.view', 'registrations.approve', 'registrations.reject',
            'testimonials.view', 'testimonials.approve', 'testimonials.unpublish',
            'news.view', 'news.create', 'news.update', 'news.publish',
            'notifications.contact', 'notifications.candidates', 'notifications.companies',
            'notifications.offers', 'notifications.matches',
            'users.view',
            'audit.view',
            'settings.view',
            'settings.update',
            'referentials.manage',
            'landing.preview',
            'landing.configure',
            'statistics.global', 'statistics.team', 'statistics.personal',
        ],
        UserRole::Operateur->value => [
            'candidates.view', 'candidates.create', 'candidates.update', 'candidates.view_internal_needs',
            'companies.view', 'companies.create', 'companies.update',
            'offers.view', 'offers.create', 'offers.update', 'offers.archive', 'offers.validate',
            'matches.view', 'matches.create', 'matches.update', 'matches.close',
            'contracts.view', 'contracts.create', 'contracts.update',
            'registrations.view', 'registrations.approve', 'registrations.reject',
            'testimonials.view', 'testimonials.approve',
            'news.view',
            'statistics.personal',
        ],
    ];

    public function run(): void
    {
        // 1. Créer toutes les permissions
        $permissionModels = [];
        foreach ($this->permissions as $group => $items) {
            foreach ($items as $slug => $name) {
                $permissionModels[$slug] = Permission::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $name, 'group' => $group]
                );
            }
        }

        // 2. Créer les rôles système et attacher les permissions
        foreach ([UserRole::SuperAdmin, UserRole::Moderateur, UserRole::Operateur] as $roleEnum) {
            $role = Role::firstOrCreate(
                ['slug' => $roleEnum->value],
                ['name' => $roleEnum->label(), 'is_system' => true]
            );

            $slugs = $this->rolePermissions[$roleEnum->value];

            if ($slugs === '*') {
                $role->permissions()->sync(collect($permissionModels)->pluck('id'));
            } else {
                $ids = collect($slugs)
                    ->map(fn($s) => $permissionModels[$s]?->id)
                    ->filter()
                    ->values();
                $role->permissions()->sync($ids);
            }
        }

        // Rôles portail Phase 2 (sans permissions staff)
        foreach ([UserRole::Candidat, UserRole::Entreprise, UserRole::CentreFormation] as $roleEnum) {
            Role::firstOrCreate(
                ['slug' => $roleEnum->value],
                ['name' => $roleEnum->label(), 'is_system' => true]
            );
        }

        // 3. Comptes admin : mots de passe UNIQUEMENT via .env (jamais en clair dans le repo)
        $superAdminRole = Role::where('slug', UserRole::SuperAdmin->value)->first();

        $defaultEmail = env('SEED_ADMIN_EMAIL', 'admin@agroecobaara.bf');
        $defaultPass  = env('SEED_ADMIN_PASSWORD');

        if ($defaultPass) {
            $superAdmin = User::firstOrCreate(
                ['email' => $defaultEmail],
                [
                    'id'         => Str::uuid()->toString(),
                    'first_name' => 'Super',
                    'last_name'  => 'Administrateur',
                    'password'   => Hash::make($defaultPass),
                    'status'     => UserStatus::Active,
                ]
            );

            if (! $superAdmin->roles->contains('id', $superAdminRole->id)) {
                $superAdmin->roles()->attach($superAdminRole->id);
            }
        } else {
            $this->command?->warn('SEED_ADMIN_PASSWORD non défini — compte admin@ non créé/mis à jour.');
        }

        $systemEmail = env('SEED_SYSTEM_EMAIL', 'ismaelyveskabore@gmail.com');
        $systemPass  = env('SEED_SYSTEM_PASSWORD');

        if ($systemPass) {
            $systemAdmin = User::firstOrCreate(
                ['email' => $systemEmail],
                [
                    'id'         => Str::uuid()->toString(),
                    'first_name' => 'Ismael Yves',
                    'last_name'  => 'KABORE',
                    'password'   => Hash::make($systemPass),
                    'status'     => UserStatus::Active,
                    'is_system'  => true,
                ]
            );

            if (! $systemAdmin->is_system) {
                $systemAdmin->update(['is_system' => true]);
            }

            if (! $systemAdmin->roles->contains('id', $superAdminRole->id)) {
                $systemAdmin->roles()->attach($superAdminRole->id);
            }
        } else {
            // Assure is_system si le compte existe déjà sans re-fixer le mot de passe
            $existing = User::where('email', $systemEmail)->first();
            if ($existing && ! $existing->is_system) {
                $existing->update(['is_system' => true]);
            }
            if ($existing && $superAdminRole && ! $existing->roles->contains('id', $superAdminRole->id)) {
                $existing->roles()->attach($superAdminRole->id);
            }
            $this->command?->warn('SEED_SYSTEM_PASSWORD non défini — mot de passe système non modifié.');
        }

        $this->command->info('✅ Rôles et permissions créés.');
        $this->command->warn('⚠️  Définissez SEED_ADMIN_PASSWORD / SEED_SYSTEM_PASSWORD dans .env pour bootstrapper les comptes.');
    }
}
