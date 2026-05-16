<?php

namespace App\Livewire\Landing;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Médiathèque — Agro Eco BAARA')]
class Mediatheque extends Component
{
    public function render()
    {
        return view('livewire.landing.mediatheque');
    }
}
