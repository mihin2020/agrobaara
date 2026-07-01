<?php

namespace App\Livewire\Offers;

use App\Enums\OfferStatus;
use App\Exports\OffersExport;
use App\Models\JobOffer;
use App\Models\ReferentialCommune;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Offres d\'emploi - Agro Eco BAARA')]
class OfferIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';
    public string $exportFrom = '';
    public string $exportTo = '';
    public string $exportStatus = '';
    public string $exportCommune = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new OffersExport(
                from: $this->exportFrom ?: null,
                to: $this->exportTo ?: null,
                status: $this->exportStatus ?: null,
                commune: $this->exportCommune ?: null,
            ),
            'offres_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function publishOffer(string $offerId): void
    {
        $offer = JobOffer::findOrFail($offerId);
        $this->authorize('publish', $offer);
        $offer->publish(Auth::user());

        activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_published');
        session()->flash('success', 'Offre publiée avec succès.');
    }

    public function archiveOffer(string $offerId): void
    {
        $offer = JobOffer::findOrFail($offerId);
        $this->authorize('archive', $offer);
        $offer->archive();

        activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_archived');
        session()->flash('success', 'Offre archivée.');
    }

    public function unarchiveOffer(string $offerId): void
    {
        $offer = JobOffer::findOrFail($offerId);
        $this->authorize('unarchive', $offer);
        $offer->unarchive();

        activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_unarchived');
        session()->flash('success', 'Offre remise en brouillon.');
    }

    public function render()
    {
        $this->authorize('viewAny', JobOffer::class);

        $user = Auth::user();

        $offers = JobOffer::with('company')
            ->when($user->isOperator(), fn($q) => $q->byOperator($user->id))
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->withCount('matches')
            ->latest()
            ->paginate(10);

        $statuses = OfferStatus::cases();
        $communes = ReferentialCommune::active()->get();

        return view('livewire.offers.offer-index', compact('offers', 'statuses', 'communes'));
    }
}
