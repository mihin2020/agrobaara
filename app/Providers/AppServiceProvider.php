<?php

namespace App\Providers;

use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\Company;
use App\Models\Contract;
use App\Models\JobOffer;
use App\Models\SiteSetting;
use App\Policies\CandidatePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ContractPolicy;
use App\Policies\JobOfferPolicy;
use App\Policies\MatchPolicy;
use App\Services\AuthService;
use App\Services\FeatureFlagService;
use App\Services\MatchingService;
use App\Services\NotificationService;
use App\Services\ReferenceService;
use App\Support\Feature;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $policies = [
        Candidate::class      => CandidatePolicy::class,
        Company::class        => CompanyPolicy::class,
        JobOffer::class       => JobOfferPolicy::class,
        CandidateMatch::class => MatchPolicy::class,
        Contract::class       => ContractPolicy::class,
    ];

    public function register(): void
    {
        $this->app->singleton(AuthService::class);
        $this->app->singleton(ReferenceService::class);
        $this->app->singleton(MatchingService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(FeatureFlagService::class);
    }

    public function boot(): void
    {
        // Forcer HTTPS + cookies sécurisés en production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->app['config']->set('session.secure', true);
            $this->app['config']->set('session.http_only', true);
            $this->app['config']->set('session.same_site', 'strict');
        }

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

        View::composer('*', function ($view) {
            $view->with('showAgroecologyMention', SiteSetting::showAgroecologyMention());
        });

        Blade::if('agroecology', function () {
            return SiteSetting::showAgroecologyMention();
        });

        Blade::if('feature', function (string $key) {
            return Feature::enabled($key);
        });
    }
}
