<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use App\Notifications\UserInvitedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nouvel utilisateur — Agro Eco BAARA')]
class UserCreate extends Component
{
    public string $first_name = '';
    public string $last_name  = '';
    public string $email      = '';
    public string $role       = '';

    public function mount(): void
    {
        $this->authorize('super_admin', User::class);
    }

    public function save(): void
    {
        $this->validate([
            'first_name' => 'required|string|min:2|max:100',
            'last_name'  => 'required|string|min:2|max:100',
            'email'      => 'required|email|max:150|unique:users,email',
            'role'       => 'required|in:' . implode(',', UserRole::values()),
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.required'      => "L'e-mail est obligatoire.",
            'email.unique'        => 'Cet e-mail est déjà utilisé.',
            'role.required'       => 'Le rôle est obligatoire.',
        ]);

        $user = User::create([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'password'   => Hash::make(Str::random(40)),
            'status'     => UserStatus::PendingPassword,
        ]);

        $user->assignRole($this->role);

        // Génère le token de réinitialisation et envoie l'invitation
        $token = Password::createToken($user);
        $user->notify(new UserInvitedNotification($token, Auth::user()));

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['role' => $this->role])
            ->log('user_created');

        session()->flash('success', "Compte créé. Un email d'invitation a été envoyé à {$user->email}.");
        $this->redirect(route('admin.admin.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.users.user-create', [
            'roles' => UserRole::options(),
        ]);
    }
}