<?php

namespace App\Livewire\Landing;

use App\Models\LandingSection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Médiathèque — Agro Eco BAARA')]
class Mediatheque extends Component
{
    public function render()
    {
        $mediaSection  = LandingSection::forSlug('mediatheque');
        $mediaContent  = $mediaSection?->content ?? [];
        $mediaTitle    = $mediaContent['title'] ?? 'MÉDIATHÈQUE';
        $mediaDesc     = $mediaContent['description'] ?? 'Découvrez nos activités à travers nos visuels.';
        $mediaPhotos   = $mediaContent['photos'] ?? [];
        $mediaCategories = $mediaContent['categories'] ?? [
            ['key' => 'terrain',   'label' => 'Terrain'],
            ['key' => 'formation', 'label' => 'Formation'],
            ['key' => 'evenement', 'label' => 'Événement'],
        ];
        $hasMediaVideos = collect($mediaPhotos)->contains(fn($p) => ($p['type'] ?? 'image') === 'video');

        return view('livewire.landing.mediatheque', compact(
            'mediaSection', 'mediaPhotos', 'mediaTitle', 'mediaDesc', 'mediaCategories', 'hasMediaVideos'
        ));
    }
}
