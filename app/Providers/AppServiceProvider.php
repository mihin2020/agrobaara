<?php

namespace App\Providers;

use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\Company;
use App\Models\JobOffer;
use App\Policies\CandidatePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\JobOfferPolicy;
use App\Policies\MatchPolicy;
use App\Services\AuthService;
use App\Services\MatchingService;
use App\Services\ReferenceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $policies = [
        Candidate::class     => CandidatePolicy::class,
        Company::class       => CompanyPolicy::class,
        JobOffer::class      => JobOfferPolicy::class,
        CandidateMatch::class => MatchPolicy::class,
    ];

    public function register(): void
    {
        $this->app->singleton(AuthService::class);
        $this->app->singleton(ReferenceService::class);
        $this->app->singleton(MatchingService::class);
    }

    public function boot(): void
    {
        // Enregistrement des Policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Super-admin bypass global : toujours autorisé
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });
    }
}
