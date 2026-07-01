<?php

namespace App\Livewire\Landing;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Politique de confidentialité - Agro Eco BAARA')]
class PrivacyPolicy extends Component
{
    public function render()
    {
        return view('livewire.landing.privacy-policy');
    }
}
