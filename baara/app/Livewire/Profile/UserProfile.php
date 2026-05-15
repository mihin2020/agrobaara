<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Mon profil — Agro Eco BAARA')]
class UserProfile extends Component
{
    // Infos du compte
    public string $first_name = '';
    public string $last_name  = '';
    public string $email      = '';

    // Changement de mot de passe
    public string $current_password      = '';
    public string $new_password          = '';
    public string $new_password_confirmation = '';

    public bool $profileSaved  = false;
    public bool $passwordSaved = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->last_name  = $user->last_name;
        $this->email      = $user->email;
    }

    public function saveProfile(): void
    {
        $user = Auth::user();

        $this->validate([
            'first_name' => 'required|string|min:2|max:100',
            'last_name'  => 'required|string|min:2|max:100',
            'email'      => 'required|email|max:150|unique:users,email,' . $user->id,
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.required'      => "L'e-mail est obligatoire.",
            'email.unique'        => 'Cet e-mail est déjà utilisé par un autre compte.',
        ]);

        $user->update([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
        ]);

        activity()->causedBy($user)->performedOn($user)->log('profile_updated');

        $this->profileSaved = true;
        $this->dispatch('profile-saved');
    }

    public function changePassword(): void
    {
        $user = Auth::user();

        $this->validate([
            'current_password'      => 'required|string',
            'new_password'          => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'new_password.required'     => 'Le nouveau mot de passe est obligatoire.',
            'new_password.confirmed'    => 'La confirmation ne correspond pas.',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Le mot de passe actuel est incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        activity()->causedBy($user)->performedOn($user)->log('password_changed');

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->passwordSaved = true;
    }

    public function render()
    {
        return view('livewire.profile.user-profile', [
            'user' => Auth::user()->load('roles'),
        ]);
    }
}