<?php

namespace App\Livewire\Landing;

use App\Models\LandingSection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Agro Eco BAARA — Emploi agroécologique au Burkina Faso')]
class LandingPage extends Component
{
    public function render()
    {
        $sections = LandingSection::allOrdered()->keyBy('slug');

        return view('livewire.landing.landing-page', compact('sections'));
    }
}
