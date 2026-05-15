<?php

use App\Livewire\Admin\Users\UserIndex;
use App\Livewire\Admin\Users\UserCreate;
use App\Livewire\Admin\Roles\RoleIndex;
use App\Http\Controllers\Auth\LoginController;
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
use App\Livewire\Matching\MatchShow;
use App\Livewire\Landing\LandingPage;
use App\Livewire\Landing\ContactForm;
use App\Livewire\Admin\Landing\LandingConfigurator;
use App\Livewire\Admin\Audit\AuditLog;
use App\Livewire\Admin\Settings\SettingsIndex;
use App\Livewire\Profile\UserProfile;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// LANDING PAGE PUBLIQUE
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', LandingPage::class)->name('home');

// ─────────────────────────────────────────────────────────────────────────────
// AUTHENTIFICATION (invités seulement)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'show'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store']);
    Route::get('/mot-de-passe-oublie', ForgotPassword::class)->name('password.request');
    Route::get('/reinitialiser-mot-de-passe/{token}', ResetPassword::class)->name('password.reset');
});

// Changement de mot de passe (1ère connexion) — utilisateur connecté
Route::middleware('auth')->group(function () {
    Route::get('/changer-mot-de-passe', ChangePassword::class)->name('password.change');
    Route::post('/deconnexion', [\App\Http\Controllers\Auth\LogoutController::class, 'destroy'])->name('logout');
});

// ─────────────────────────────────────────────────────────────────────────────
// BACK-OFFICE (authentifié + compte actif)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Tableau de bord
    Route::get('/tableau-de-bord', OperatorDashboard::class)->name('dashboard');

    // Profil utilisateur
    Route::get('/profil', UserProfile::class)->name('profile');

    // ── Candidats ──────────────────────────────────────────────────────────
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

    // ── Entreprises ────────────────────────────────────────────────────────
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

    // ── Offres ─────────────────────────────────────────────────────────────
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

    // ── Matching ───────────────────────────────────────────────────────────
    Route::prefix('matching')->name('matches.')->group(function () {
        Route::get('/', MatchIndex::class)->name('index');
        Route::get('/{match}', MatchShow::class)->name('show');
    });

    // ── Administration (Super-admin + Modérateur) ──────────────────────────
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
             ->middleware('role:super_admin')
             ->name('landing.configurator');
    });
});
