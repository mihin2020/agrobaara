<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;

class CandidatePolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('candidates.view');
    }

    public function view(User $user, Candidate $candidate): bool
    {
        return $user->hasPermission('candidates.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('candidates.create');
    }

    public function update(User $user, Candidate $candidate): bool
    {
        if (!$user->hasPermission('candidates.update')) {
            return false;
        }

        // Opérateur : seulement ses propres candidats
        if ($user->isOperator()) {
            return $candidate->created_by === $user->id;
        }

        return true; // Modérateur et super-admin
    }

    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->hasPermission('candidates.delete');
    }

    public function export(User $user): bool
    {
        return $user->hasPermission('candidates.export');
    }

    public function viewInternalNeeds(User $user): bool
    {
        return $user->hasPermission('candidates.view_internal_needs');
    }
}
