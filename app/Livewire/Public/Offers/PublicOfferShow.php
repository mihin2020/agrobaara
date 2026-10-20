<?php

namespace App\Livewire\Public\Offers;

use App\Enums\OfferStatus;
use App\Models\JobOffer;
use App\Support\Feature;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Offre - Agro Eco BAARA')]
class PublicOfferShow extends Component
{
    public JobOffer $offer;

    public function mount(JobOffer $offer): void
    {
        abort_unless(Feature::enabled('public_offers'), 404);
        abort_unless($offer->status === OfferStatus::Publiee, 404);
        $this->offer = $offer->load('company', 'skills');
    }

    public function render()
    {
        return view('livewire.public.offers.public-offer-show');
    }
}
