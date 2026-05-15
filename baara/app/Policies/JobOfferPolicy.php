<?php

namespace App\Policies;

use App\Models\JobOffer;
use App\Models\User;

class JobOfferPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('offers.view');
    }

    public function view(User $user, JobOffer $offer): bool
    {
        return $user->hasPermission('offers.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('offers.create');
    }

    public function update(User $user, JobOffer $offer): bool
    {
        if (!$user->hasPermission('offers.update')) {
            return false;
        }

        if ($user->isOperator()) {
            return $offer->created_by === $user->id;
        }

        return true;
    }

    public function publish(User $user): bool
    {
        return $user->hasPermission('offers.publish');
    }

    public function archive(User $user): bool
    {
        return $user->hasPermission('offers.archive');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission('offers.delete');
    }
}
