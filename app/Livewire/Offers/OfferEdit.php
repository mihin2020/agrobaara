<?php

namespace App\Livewire\Offers;

use App\Models\JobOffer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Modifier offre — Agro Eco BAARA')]
class OfferEdit extends Component
{
    public JobOffer $offer;

    public function mount(JobOffer $offer): void
    {
        $this->authorize('update', $offer);
        $this->offer = $offer;
    }

    public function render()
    {
        return view('livewire.offers.offer-edit');
    }
}
