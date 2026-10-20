<?php

namespace App\Livewire\Admin\Offers;

use App\Enums\OfferStatus;
use App\Models\JobOffer;
use App\Support\Feature;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Offres à valider')]
class OfferValidationIndex extends Component
{
    use WithPagination;

    public function mount(): void
    {
        abort_unless(Auth::user()->hasPermission('offers.validate'), 403);
    }

    public function approve(string $id): void
    {
        abort_unless(Auth::user()->hasPermission('offers.validate') || Auth::user()->hasPermission('offers.publish'), 403);
        $offer = JobOffer::findOrFail($id);
        abort_unless($offer->status === OfferStatus::EnAttente, 422);
        $offer->publish(Auth::user());
        activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_validated_from_portal');
        session()->flash('success', 'Offre publiée.');
    }

    public function reject(string $id): void
    {
        abort_unless(Auth::user()->hasPermission('offers.validate'), 403);
        $offer = JobOffer::findOrFail($id);
        $offer->update(['status' => OfferStatus::Brouillon]);
        activity()->causedBy(Auth::user())->performedOn($offer)->log('offer_validation_rejected');
        session()->flash('success', 'Offre renvoyée en brouillon.');
    }

    public function render()
    {
        return view('livewire.admin.offers.offer-validation-index', [
            'offers' => JobOffer::with('company:id,name,reference')
                ->pendingValidation()
                ->latest()
                ->paginate(20),
            'autoMatch' => Feature::autoMatch(),
        ]);
    }
}
