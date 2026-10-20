<?php

use App\Livewire\Admin\Users\UserIndex;
use App\Livewire\Admin\Users\UserCreate;
use App\Livewire\Admin\Roles\RoleIndex;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\SecureFileController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Dashboard\OperatorDashboard;
use App\Livewire\Candidates\CandidateIndex;
use App\Livewire\Candidates\CandidateCreate;
use App\Livewire\Candidates\CandidateEdit;
use App\Livewire\Candidates\CandidateShow;
use App\Livewire\Companies\CompanyIndex;
use App\Livewire\Companies\CompanyCreate;
use App\Livewire\Companies\CompanyEdit;
use App\Livewire\Companies\CompanyShow;
use App\Livewire\Offers\OfferIndex;
use App\Livewire\Offers\OfferCreate;
use App\Livewire\Offers\OfferEdit;
use App\Livewire\Offers\OfferShow;
use App\Livewire\Matching\MatchIndex;
use App\Livewire\Matching\MatchCreate;
use App\Livewire\Matching\MatchShow;
use App\Livewire\Contracts\ContractIndex;
use App\Livewire\Contracts\ContractCreate;
use App\Livewire\Contracts\ContractShow;
use App\Livewire\Landing\LandingPage;
use App\Livewire\Landing\Mediatheque;
use App\Livewire\Landing\Bibliotheque;
use App\Livewire\Landing\PrivacyPolicy;
use App\Livewire\Admin\Landing\LandingConfigurator;
use App\Livewire\Admin\Audit\AuditLog;
use App\Livewire\Admin\Settings\SettingsIndex;
use App\Livewire\Admin\Library\LibraryIndex;
use App\Livewire\Admin\Messages\ContactMessageIndex;
use App\Livewire\Admin\Phase2\FeatureFlagsPanel;
use App\Livewire\Admin\Registrations\RegistrationIndex;
use App\Livewire\Admin\Offers\OfferValidationIndex;
use App\Livewire\Admin\Testimonials\TestimonialIndex;
use App\Livewire\Admin\News\NewsIndex as AdminNewsIndex;
use App\Livewire\Public\Register\RegisterChoice;
use App\Livewire\Public\Register\RegisterCandidate;
use App\Livewire\Public\Register\RegisterCompany;
use App\Livewire\Public\Offers\PublicOfferIndex;
use App\Livewire\Public\Offers\PublicOfferShow;
use App\Livewire\Public\News\NewsIndex as PublicNewsIndex;
use App\Livewire\Public\News\NewsShow as PublicNewsShow;
use App\Livewire\Portal\Candidate\CandidateDashboard;
use App\Livewire\Portal\Company\CompanyDashboard;
use App\Livewire\Profile\UserProfile;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// LANDING PAGE PUBLIQUE
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/', LandingPage::class)->name('home');
    Route::get('/mediatheque', Mediatheque::class)->name('mediatheque');
    Route::get('/bibliotheque', Bibliotheque::class)->name('bibliotheque');
    Route::get('/politique-de-confidentialite', PrivacyPolicy::class)->name('privacy');
});

Route::middleware(['feature:public_offers', 'throttle:60,1'])->group(function () {
    Route::get('/offres', PublicOfferIndex::class)->name('public.offers.index');
    Route::get('/offres/{offer}', PublicOfferShow::class)->name('public.offers.show');
});

Route::middleware(['feature:news_ribbon', 'throttle:60,1'])->group(function () {
    Route::get('/actualites', PublicNewsIndex::class)->name('public.news.index');
    Route::get('/actualites/{slug}', PublicNewsShow::class)->name('public.news.show');
});

Route::middleware(['guest', 'feature:self_registration', 'throttle:20,1'])->prefix('inscription')->name('register.')->group(function () {
    Route::get('/', RegisterChoice::class)->name('choice');
    Route::get('/candidat', RegisterCandidate::class)->name('candidate');
    Route::get('/entreprise', RegisterCompany::class)
        ->defaults('type', 'entreprise')
        ->name('company');
    Route::get('/centre-formation', RegisterCompany::class)
        ->defaults('type', 'centre_formation')
        ->name('center');
});

// ─────────────────────────────────────────────────────────────────────────────
// AUTHENTIFICATION (invités seulement)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['guest', 'throttle:30,1'])->group(function () {
    Route::get('/connexion', [LoginController::class, 'show'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/mot-de-passe-oublie', ForgotPassword::class)->name('password.request');
    Route::get('/reinitialiser-mot-de-passe/{token}', ResetPassword::class)->name('password.reset');

    Route::get('/connexion/verification-2fa', [TwoFactorChallengeController::class, 'show'])->name('login.2fa');
    Route::post('/connexion/verification-2fa', [TwoFactorChallengeController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('login.2fa.verify');
    Route::post('/connexion/verification-2fa/renvoyer', [TwoFactorChallengeController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('login.2fa.resend');
    Route::post('/connexion/verification-2fa/annuler', [TwoFactorChallengeController::class, 'cancel'])
        ->name('login.2fa.cancel');
});

Route::middleware('auth')->group(function () {
    Route::get('/changer-mot-de-passe', ChangePassword::class)->name('password.change');
    Route::post('/deconnexion', [\App\Http\Controllers\Auth\LogoutController::class, 'destroy'])->name('logout');
});

// ─────────────────────────────────────────────────────────────────────────────
// PORTAILS Phase 2
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('espace')->name('portal.')->group(function () {
    Route::get('/candidat', CandidateDashboard::class)
        ->middleware('feature:portal_candidate')
        ->name('candidate.dashboard');
    Route::get('/entreprise', CompanyDashboard::class)
        ->middleware('feature:portal_company')
        ->name('company.dashboard');
    Route::get('/centre', CompanyDashboard::class)
        ->middleware('feature:portal_training_center')
        ->name('center.dashboard');
});

// ─────────────────────────────────────────────────────────────────────────────
// BACK-OFFICE
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/tableau-de-bord', OperatorDashboard::class)->name('dashboard');
    Route::get('/profil', UserProfile::class)->name('profile');

    Route::get('/fichiers/candidats/{candidate}', [SecureFileController::class, 'candidateDocument'])
        ->middleware('throttle:60,1')
        ->name('secure.candidate-file');

    Route::prefix('candidats')->name('candidates.')->group(function () {
        Route::get('/', CandidateIndex::class)->name('index');
        Route::get('/nouveau', CandidateCreate::class)
             ->middleware('permission:candidates.create')
             ->name('create');
        Route::get('/{candidate}', CandidateShow::class)->name('show');
        Route::get('/{candidate}/modifier', CandidateEdit::class)
             ->middleware('permission:candidates.update')
             ->name('edit');
    });

    Route::prefix('entreprises')->name('companies.')->group(function () {
        Route::get('/', CompanyIndex::class)->name('index');
        Route::get('/nouvelle', CompanyCreate::class)
             ->middleware('permission:companies.create')
             ->name('create');
        Route::get('/{company}', CompanyShow::class)->name('show');
        Route::get('/{company}/modifier', CompanyEdit::class)
             ->middleware('permission:companies.update')
             ->name('edit');
    });

    Route::prefix('offres')->name('offers.')->group(function () {
        Route::get('/', OfferIndex::class)->name('index');
        Route::get('/nouvelle', OfferCreate::class)
             ->middleware('permission:offers.create')
             ->name('create');
        Route::get('/{offer}', OfferShow::class)->name('show');
        Route::get('/{offer}/modifier', OfferEdit::class)
             ->middleware('permission:offers.update')
             ->name('edit');
    });

    Route::prefix('matching')->name('matches.')->group(function () {
        Route::get('/', MatchIndex::class)->name('index');
        Route::get('/nouveau', MatchCreate::class)
             ->middleware('permission:matches.create')
             ->name('create');
        Route::get('/{match}', MatchShow::class)->name('show');
    });

    Route::middleware('feature:phase2_master')->group(function () {
        Route::prefix('contrats')->name('contracts.')->group(function () {
            Route::get('/', ContractIndex::class)->name('index');
            Route::get('/nouveau', ContractCreate::class)
                 ->middleware('permission:contracts.create')
                 ->name('create');
            Route::get('/{contract}', ContractShow::class)->name('show');
        });

        Route::get('/inscriptions', RegistrationIndex::class)
            ->middleware('permission:registrations.view')
            ->name('registrations.index');

        Route::get('/offres-a-valider', OfferValidationIndex::class)
            ->middleware('permission:offers.validate')
            ->name('offers.validate');

        Route::get('/temoignages', TestimonialIndex::class)
            ->middleware('permission:testimonials.view')
            ->name('testimonials.index');

        Route::get('/actualites', AdminNewsIndex::class)
            ->middleware('permission:news.view')
            ->name('news.index');
    });

    Route::get('/messages', ContactMessageIndex::class)->name('messages.index');

    // Accessible même Phase 2 OFF — pour pouvoir activer le flag maître
    Route::get('/phase-2', FeatureFlagsPanel::class)->name('phase2.flags');

    Route::middleware('role:super_admin')
        ->prefix('bibliotheque')
        ->name('library.')
        ->group(function () {
            Route::get('/', LibraryIndex::class)->name('index');
        });

    Route::middleware('role:super_admin,moderateur')->prefix('administration')->name('admin.')->group(function () {

        Route::get('/utilisateurs', UserIndex::class)->name('users.index');
        Route::get('/utilisateurs/nouveau', UserCreate::class)
             ->middleware('role:super_admin')
             ->name('users.create');

        Route::get('/roles-permissions', RoleIndex::class)
             ->middleware('role:super_admin')
             ->name('roles.index');

        Route::get('/journal-audit', AuditLog::class)->name('audit.index');

        Route::get('/parametres', SettingsIndex::class)->name('settings.index');

        Route::get('/landing', LandingConfigurator::class)
             ->middleware('permission:landing.configure')
             ->name('landing.configurator');
    });
});
