<?php

namespace App\Livewire\Contracts;

use App\Enums\ContractStatus;
use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\Company;
use App\Models\Contract;
use App\Models\JobOffer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nouveau contrat - Agro Eco BAARA')]
class ContractCreate extends Component
{
    #[Url]
    public string $match_id = '';

    public string $candidate_id = '';
    public string $company_id = '';
    public string $offer_id = '';
    public string $negotiated_salary = '';
    public string $notes = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $duration_label = '';

    public string $candidateSearch = '';
    public string $companySearch = '';
    public string $offerSearch = '';

    public bool $fromMatch = false;

    public function mount(): void
    {
        $this->authorize('create', Contract::class);

        if ($this->match_id !== '') {
            $match = CandidateMatch::with('offer.company')->findOrFail($this->match_id);

            if ($match->status !== MatchStatus::Acceptee) {
                abort(403, 'Un contrat ne peut être créé que pour une mise en relation acceptée.');
            }

            if (Contract::where('match_id', $match->id)->exists()) {
                $existing = Contract::where('match_id', $match->id)->first();
                $this->redirect(route('admin.contracts.show', $existing), navigate: true);

                return;
            }

            $this->fromMatch = true;
            $this->candidate_id = $match->candidate_id;
            $this->offer_id = $match->offer_id;
            $this->company_id = $match->offer->company_id;
            $this->applyOfferPeriod($match->offer);
        }
    }

    public function updatedOfferId(): void
    {
        if ($this->offer_id === '') {
            return;
        }
        $offer = JobOffer::find($this->offer_id);
        if ($offer) {
            $this->company_id = $offer->company_id;
            $this->applyOfferPeriod($offer);
        }
    }

    protected function applyOfferPeriod(?JobOffer $offer): void
    {
        if (! $offer) {
            return;
        }
        $attrs = Contract::attributesFromOffer($offer);
        $this->duration_label = $attrs['duration_label'] ?? '';
        $this->starts_at = $attrs['starts_at'] ?? '';
        $this->ends_at = $attrs['ends_at'] ?? '';
        if ($offer->salary_range && $this->negotiated_salary === '') {
            $this->negotiated_salary = $offer->salary_range;
        }
        $this->recalculateEndsAt();
    }

    public function updatedStartsAt(): void
    {
        $this->recalculateEndsAt();
    }

    public function updatedDurationLabel(): void
    {
        $this->recalculateEndsAt();
    }

    public function recalculateEndsAt(): void
    {
        if ($this->starts_at === '' || trim((string) $this->duration_label) === '') {
            return;
        }

        try {
            $end = Contract::estimateEndDate(
                \Carbon\Carbon::parse($this->starts_at)->startOfDay(),
                $this->duration_label
            );
            if ($end) {
                $this->ends_at = $end->format('Y-m-d');
            }
        } catch (\Throwable) {
            // ignore parse errors while typing
        }
    }

    public function selectCandidate(string $id): void
    {
        $this->candidate_id = $id;
        $this->candidateSearch = '';
        $this->resetValidation('candidate_id');
    }

    public function clearCandidate(): void
    {
        if ($this->fromMatch) {
            return;
        }
        $this->candidate_id = '';
    }

    public function selectCompany(string $id): void
    {
        $this->company_id = $id;
        $this->companySearch = '';
        if ($this->offer_id) {
            $offer = JobOffer::find($this->offer_id);
            if ($offer && $offer->company_id !== $id) {
                $this->offer_id = '';
            }
        }
        $this->resetValidation('company_id');
    }

    public function clearCompany(): void
    {
        if ($this->fromMatch) {
            return;
        }
        $this->company_id = '';
        $this->offer_id = '';
    }

    public function selectOffer(string $id): void
    {
        $offer = JobOffer::findOrFail($id);
        $this->offer_id = $id;
        $this->company_id = $offer->company_id;
        $this->applyOfferPeriod($offer);
        $this->offerSearch = '';
        $this->resetValidation(['offer_id', 'company_id']);
    }

    public function clearOffer(): void
    {
        if ($this->fromMatch) {
            return;
        }
        $this->offer_id = '';
    }

    public function save(): void
    {
        $this->authorize('create', Contract::class);

        $matchId = $this->fromMatch ? $this->match_id : null;

        $this->validate([
            'candidate_id' => 'required|uuid|exists:candidates,id',
            'company_id'   => 'required|uuid|exists:companies,id',
            'offer_id'     => [
                'required',
                'uuid',
                'exists:job_offers,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $offer = JobOffer::find($value);
                    if ($offer && $offer->company_id !== $this->company_id) {
                        $fail('L\'offre sélectionnée n\'appartient pas à cette entreprise.');
                    }
                },
            ],
            'negotiated_salary' => 'nullable|string|max:150',
            'starts_at'         => 'nullable|date',
            'ends_at'           => 'nullable|date|after_or_equal:starts_at',
            'duration_label'    => 'nullable|string|max:150',
            'notes'             => 'nullable|string|max:5000',
        ], [
            'candidate_id.required' => 'Sélectionnez un candidat.',
            'company_id.required'   => 'Sélectionnez une entreprise.',
            'offer_id.required'     => 'Sélectionnez une offre.',
            'ends_at.after_or_equal'=> 'La fin doit être postérieure ou égale au début.',
        ]);

        if ($matchId) {
            $match = CandidateMatch::findOrFail($matchId);
            if ($match->status !== MatchStatus::Acceptee) {
                $this->addError('match_id', 'La mise en relation n\'est pas acceptée.');
                return;
            }
            if (Contract::where('match_id', $matchId)->exists()) {
                $this->addError('match_id', 'Un contrat existe déjà pour cette mise en relation.');
                return;
            }
        }

        $offer = JobOffer::findOrFail($this->offer_id);
        $referenceService = app(\App\Services\ReferenceService::class);

        $contract = Contract::create([
            'reference'         => $referenceService->generateContractReference(),
            'match_id'          => $matchId,
            'candidate_id'      => $this->candidate_id,
            'company_id'        => $this->company_id,
            'offer_id'          => $this->offer_id,
            'negotiated_salary' => $this->negotiated_salary ?: null,
            'starts_at'         => $this->starts_at ?: null,
            'ends_at'           => $this->ends_at ?: null,
            'duration_label'    => $this->duration_label ?: ($offer->duration ?: null),
            'is_renewable'      => true,
            'status'            => ContractStatus::Brouillon,
            'notes'             => $this->notes ?: null,
            'clauses_snapshot'  => Contract::buildClausesSnapshot($offer),
            'created_by'        => Auth::id(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($contract)
            ->withProperties([
                'match_id'     => $matchId,
                'candidate_id' => $this->candidate_id,
                'company_id'   => $this->company_id,
                'offer_id'     => $this->offer_id,
            ])
            ->log('contract_created');

        session()->flash('success', 'Contrat créé avec succès.');
        $this->redirect(route('admin.contracts.show', $contract), navigate: true);
    }

    public function render()
    {
        $selectedCandidate = $this->candidate_id
            ? Candidate::with('commune')->find($this->candidate_id)
            : null;

        $selectedCompany = $this->company_id
            ? Company::find($this->company_id)
            : null;

        $selectedOffer = $this->offer_id
            ? JobOffer::with('company')->find($this->offer_id)
            : null;

        $candidates = collect();
        if (!$this->fromMatch && $this->candidateSearch !== '' && strlen($this->candidateSearch) >= 2) {
            $term = '%' . $this->candidateSearch . '%';
            $candidates = Candidate::with('commune')
                ->where(function ($q) use ($term) {
                    $q->where('first_name', 'like', $term)
                      ->orWhere('last_name', 'like', $term)
                      ->orWhere('reference', 'like', $term)
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term]);
                })
                ->orderBy('last_name')
                ->limit(15)
                ->get();
        }

        $companies = collect();
        if (!$this->fromMatch && $this->companySearch !== '' && strlen($this->companySearch) >= 2) {
            $term = '%' . $this->companySearch . '%';
            $companies = Company::search($term)->orderBy('name')->limit(15)->get();
        }

        $offers = collect();
        if (!$this->fromMatch && $this->offerSearch !== '' && strlen($this->offerSearch) >= 2) {
            $term = '%' . $this->offerSearch . '%';
            $offers = JobOffer::with('company')
                ->when($this->company_id, fn ($q) => $q->where('company_id', $this->company_id))
                ->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                      ->orWhere('reference', 'like', $term);
                })
                ->whereIn('status', [OfferStatus::Brouillon, OfferStatus::Publiee])
                ->latest()
                ->limit(15)
                ->get();
        }

        return view('livewire.contracts.contract-create', compact(
            'selectedCandidate',
            'selectedCompany',
            'selectedOffer',
            'candidates',
            'companies',
            'offers',
        ));
    }
}
