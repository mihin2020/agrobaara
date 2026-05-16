<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('companies.view');
    }

    public function view(User $user, Company $company): bool
    {
        return $user->hasPermission('companies.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('companies.create');
    }

    public function update(User $user, Company $company): bool
    {
        if (!$user->hasPermission('companies.update')) {
            return false;
        }

        if ($user->isOperator()) {
            return $company->created_by === $user->id;
        }

        return true;
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission('companies.delete');
    }

    public function export(User $user): bool
    {
        return $user->hasPermission('companies.export');
    }
}
