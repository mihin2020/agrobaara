<?php

namespace App\Livewire\Public\Register;

use App\Enums\ApprovalStatus;
use App\Enums\CompanyStatus;
use App\Enums\EntityType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Services\ReferenceService;
use App\Support\Feature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Inscription organisation - Agro Eco BAARA')]
class RegisterCompany extends Component
{
    public string $entity_type = 'entreprise';

    public string $name = '';

    public string $legal_rep_first_name = '';

    public string $legal_rep_last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        abort_unless(Feature::enabled('self_registration'), 404);
        $routeName = request()->route()?->getName();
        $this->entity_type = $routeName === 'register.center'
            ? 'centre_formation'
            : 'entreprise';
    }

    public function submit(ReferenceService $refs): void
    {
        abort_unless(Feature::enabled('self_registration'), 404);

        $key = 'register:org:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Trop d\'inscriptions depuis cette adresse. Réessayez plus tard.');
            return;
        }
        RateLimiter::hit($key, 3600);

        $this->validate([
            'name'                 => 'required|string|min:2|max:200',
            'legal_rep_first_name' => 'required|string|max:100',
            'legal_rep_last_name'  => 'required|string|max:100',
            'email'                => 'required|email|unique:users,email',
            'phone'                => 'required|string|max:30',
            'password'             => 'required|string|min:8|confirmed',
            'entity_type'          => 'required|in:entreprise,centre_formation',
        ]);

        $roleSlug = $this->entity_type === 'centre_formation'
            ? UserRole::CentreFormation->value
            : UserRole::Entreprise->value;

        DB::transaction(function () use ($refs, $roleSlug) {
            $user = User::create([
                'first_name' => $this->legal_rep_first_name,
                'last_name'  => $this->legal_rep_last_name,
                'email'      => $this->email,
                'password'   => Hash::make($this->password),
                'status'     => UserStatus::Inactive,
            ]);

            $role = Role::where('slug', $roleSlug)->firstOrFail();
            $user->roles()->attach($role->id);

            Company::create([
                'reference'            => $refs->generateCompanyReference(),
                'user_id'              => $user->id,
                'name'                 => $this->name,
                'entity_type'          => EntityType::from($this->entity_type),
                'status'               => CompanyStatus::Autre,
                'legal_rep_first_name' => $this->legal_rep_first_name,
                'legal_rep_last_name'  => $this->legal_rep_last_name,
                'phone'                => $this->phone,
                'email'                => $this->email,
                'approval_status'      => ApprovalStatus::Pending,
                'activity_types'       => [],
                'created_by'           => $user->id,
            ]);
        });

        session()->flash('success', 'Inscription enregistrée. Validation opérateur requise avant connexion.');
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.public.register.register-company');
    }
}
