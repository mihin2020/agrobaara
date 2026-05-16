<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Définir votre mot de passe — Agro Eco BAARA')]
class ChangePassword extends Component
{
    public string $password = '';
    public string $password_confirmation = '';

    public function save(AuthService $authService): void
    {
        $this->validate([
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.confirmed'  => 'La confirmation ne correspond pas.',
            'password.min'        => 'Minimum 8 caractères.',
        ]);

        $authService->forcePasswordChange(Auth::user(), $this->password);

        session()->flash('success', 'Mot de passe défini. Bienvenue !');
        $this->redirect(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.change-password');
    }
}
