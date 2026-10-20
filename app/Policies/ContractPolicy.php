<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('contracts.view');
    }

    public function view(User $user, Contract $contract): bool
    {
        if (! $user->hasPermission('contracts.view')) {
            return false;
        }

        if ($user->isOperator()) {
            return $contract->created_by === $user->id
                || $contract->match?->operator_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('contracts.create');
    }

    public function update(User $user, Contract $contract): bool
    {
        if (! $user->hasPermission('contracts.update')) {
            return false;
        }

        if ($user->isOperator()) {
            return $contract->created_by === $user->id
                || $contract->match?->operator_id === $user->id;
        }

        return true;
    }
}
