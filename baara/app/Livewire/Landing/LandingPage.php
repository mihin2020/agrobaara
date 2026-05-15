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
        // Charge toutes les sections actives depuis la BD
        $dbSections = LandingSection::allOrdered()->keyBy('slug');

        $hero        = $dbSections->get('hero')?->content        ?? [];
        $pourQui     = $dbSections->get('pour_qui')?->content    ?? [];
        $services    = $dbSections->get('services')?->content    ?? [];
        $comment     = $dbSections->get('comment')?->content     ?? [];
        $partenaires = $dbSections->get('partenaires')?->content ?? [];
        $contact     = $dbSections->get('contact')?->content     ?? [];

        $sectionVisibility = $dbSections->mapWithKeys(
            fn($s) => [$s->slug => $s->is_active]
        )->toArray();

        return view('livewire.landing.landing-page', compact(
            'hero', 'pourQui', 'services', 'comment', 'partenaires', 'contact', 'sectionVisibility'
        ));
    }
}