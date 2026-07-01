<?php

namespace App\Livewire\Landing;

use App\Models\LandingSection;
use App\Models\LibraryDocument;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Bibliothèque - Agro Eco BAARA')]
class Bibliotheque extends Component
{
    public function render()
    {
        $heroLogo   = LandingSection::forSlug('hero')?->content['logo_url'] ?? '/images/logo.jpeg';
        $documents  = LibraryDocument::latest()->get();

        return view('livewire.landing.bibliotheque', compact('heroLogo', 'documents'));
    }
}
