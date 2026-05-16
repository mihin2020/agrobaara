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
            'offers.view'    => 'Voir les offres',
            'offers.create'  => 'Créer une offre',
            'offers.update'  => 'Modifier une offre',
            'offers.delete'  => 'Supprimer une offre',
            'offers.publish' => 'Publier une offre',
            'offers.archive' => 'Archiver une offre',
        ],
        // Matching
        'matches' => [
            'matches.view'   => 'Voir les mises en relation',
            'matches.create' => 'Créer une mise en relation',
            'matches.update' => 'Modifier une mise en relation',
            'matches.close'  => 'Clôturer une mise en relation',
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
            'offers.view', 'offers.create', 'offers.update', 'offers.delete', 'offers.publish', 'offers.archive',
            'matches.view', 'matches.create', 'matches.update', 'matches.close',
            'users.view',
            'audit.view',
            'settings.view',
            'landing.preview',
            'statistics.global', 'statistics.team', 'statistics.personal',
        ],
        UserRole::Operateur->value => [
            'candidates.view', 'candidates.create', 'candidates.update', 'candidates.view_internal_needs',
            'companies.view', 'companies.create', 'companies.update',
            'offers.view', 'offers.create', 'offers.update', 'offers.archive',
            'matches.view', 'matches.create', 'matches.update', 'matches.close',
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

        // 3. Créer le super-administrateur par défaut
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@agroecobaara.bf'],
            [
                'id'         => Str::uuid()->toString(),
                'first_name' => 'Super',
                'last_name'  => 'Administrateur',
                'password'   => Hash::make('Admin@2027!'),
                'status'     => UserStatus::Active,
            ]
        );

        $superAdminRole = Role::where('slug', UserRole::SuperAdmin->value)->first();
        if (!$superAdmin->roles->contains('id', $superAdminRole->id)) {
            $superAdmin->roles()->attach($superAdminRole->id);
        }

        $this->command->info('✅ Rôles, permissions et super-admin créés.');
        $this->command->warn('⚠️  Changez le mot de passe admin@agroecobaara.bf / Admin@2027! en production !');
    }
}
