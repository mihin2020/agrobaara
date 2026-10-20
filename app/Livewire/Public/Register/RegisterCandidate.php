<?php

namespace App\Livewire\Public\Register;

use App\Enums\ApprovalStatus;
use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Candidate;
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
#[Title('Inscription candidat - Agro Eco BAARA')]
class RegisterCandidate extends Component
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $gender = 'M';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        abort_unless(Feature::enabled('self_registration'), 404);
    }

    public function submit(ReferenceService $refs): void
    {
        abort_unless(Feature::enabled('self_registration'), 404);

        $key = 'register:candidate:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Trop d\'inscriptions depuis cette adresse. Réessayez plus tard.');
            return;
        }
        RateLimiter::hit($key, 3600);

        $this->validate([
            'first_name' => 'required|string|min:2|max:100',
            'last_name'  => 'required|string|min:2|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string|max:30',
            'gender'     => 'required|in:M,F',
            'password'   => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'Cet e-mail est déjà utilisé.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        DB::transaction(function () use ($refs) {
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name'  => $this->last_name,
                'email'      => $this->email,
                'password'   => Hash::make($this->password),
                'status'     => UserStatus::Inactive,
            ]);

            $role = Role::where('slug', UserRole::Candidat->value)->firstOrFail();
            $user->roles()->attach($role->id);

            Candidate::create([
                'reference'       => $refs->generateCandidateReference(),
                'user_id'         => $user->id,
                'first_name'      => $this->first_name,
                'last_name'       => $this->last_name,
                'gender'          => Gender::from($this->gender),
                'phone'           => $this->phone,
                'email'           => $this->email,
                'approval_status' => ApprovalStatus::Pending,
                'birth_date'      => now()->subYears(20)->toDateString(),
                'nationality'     => 'Burkinabè',
                'created_by'      => $user->id,
            ]);
        });

        session()->flash('success', 'Inscription enregistrée. Un opérateur validera votre compte sous peu.');
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.public.register.register-candidate');
    }
}
