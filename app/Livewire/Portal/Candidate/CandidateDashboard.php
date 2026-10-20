<?php

namespace App\Livewire\Portal\Candidate;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Testimonial;
use App\Services\MatchingService;
use App\Support\Feature;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Espace candidat')]
class CandidateDashboard extends Component
{
    public string $testimonial_body = '';

    public string $testimonial_role = '';

    public ?string $training_center_id = null;

    public bool $showSuggestedOffers = false;

    public function mount(): void
    {
        abort_unless(Feature::enabled('portal_candidate'), 404);
        abort_unless(Auth::user()->hasRole(UserRole::Candidat), 403);
        $this->training_center_id = Auth::user()->candidateProfile?->training_center_id;
    }

    public function toggleSuggestedOffers(): void
    {
        $this->showSuggestedOffers = ! $this->showSuggestedOffers;
    }

    public function saveAffiliation(): void
    {
        $candidate = Auth::user()->candidateProfile;
        abort_unless($candidate, 404);
        $this->validate([
            'training_center_id' => [
                'nullable',
                'uuid',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! $value) {
                        return;
                    }
                    $ok = Company::trainingCenters()->where('id', $value)->exists();
                    if (! $ok) {
                        $fail('Le centre de formation sélectionné est invalide.');
                    }
                },
            ],
        ]);
        $candidate->update(['training_center_id' => $this->training_center_id ?: null]);
        session()->flash('success', 'Affiliation mise à jour.');
    }

    public function submitTestimonial(): void
    {
        abort_unless(Feature::enabled('testimonials'), 404);
        $candidate = Auth::user()->candidateProfile;
        $this->validate([
            'testimonial_body' => 'required|string|min:20|max:2000',
            'testimonial_role' => 'nullable|string|max:120',
        ]);
        Testimonial::create([
            'candidate_id' => $candidate->id,
            'author_name'  => $candidate->full_name,
            'role_label'   => $this->testimonial_role ?: 'Candidat',
            'body'         => $this->testimonial_body,
            'status'       => 'pending',
        ]);
        $this->reset('testimonial_body', 'testimonial_role');
        session()->flash('success', 'Témoignage soumis — en attente de validation.');
    }

    public function render(MatchingService $matching)
    {
        $candidate = Auth::user()->candidateProfile;
        $offers = ($candidate && $this->showSuggestedOffers)
            ? $matching->suggestOffersForCandidate($candidate, 10)
            : collect();
        $matches = $candidate?->matches()->with('offer.company')->latest()->limit(20)->get() ?? collect();
        $contracts = $candidate
            ? Contract::forCandidate($candidate->id)->withList()->latest()->limit(20)->get()
            : collect();
        $centers = Company::trainingCenters()->orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.candidate.candidate-dashboard', compact(
            'candidate', 'offers', 'matches', 'contracts', 'centers'
        ));
    }
}
