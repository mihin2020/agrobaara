<?php

namespace App\Livewire\Public\Offers;

use App\Models\JobOffer;
use App\Support\Feature;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.landing')]
#[Title('Offres d\'emploi - Agro Eco BAARA')]
class PublicOfferIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        abort_unless(Feature::enabled('public_offers'), 404);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $offers = JobOffer::query()
            ->published()
            ->with('company')
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->latest('published_at')
            ->paginate(12);

        return view('livewire.public.offers.public-offer-index', compact('offers'));
    }
}
