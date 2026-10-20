<?php

namespace App\Livewire\Public\Register;

use App\Support\Feature;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Inscription - Agro Eco BAARA')]
class RegisterChoice extends Component
{
    public function mount(): void
    {
        abort_unless(Feature::enabled('self_registration'), 404);
    }

    public function render()
    {
        return view('livewire.public.register.register-choice');
    }
}
