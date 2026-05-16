<?php

namespace App\Policies;

use App\Models\CandidateMatch;
use App\Models\User;

class MatchPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('matches.view');
    }

    public function view(User $user, CandidateMatch $match): bool
    {
        if (!$user->hasPermission('matches.view')) {
            return false;
        }

        if ($user->isOperator()) {
            return $match->operator_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('matches.create');
    }

    public function update(User $user, CandidateMatch $match): bool
    {
        if (!$user->hasPermission('matches.update')) {
            return false;
        }

        if ($user->isOperator()) {
            return $match->operator_id === $user->id;
        }

        return true;
    }

    public function close(User $user, CandidateMatch $match): bool
    {
        if (!$user->hasPermission('matches.close')) {
            return false;
        }

        if ($user->isOperator()) {
            return $match->operator_id === $user->id;
        }

        return true;
    }
}
