<?php

namespace App\Livewire\Matching;

use App\Enums\MatchStatus;
use App\Models\CandidateMatch;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Mise en relation — Agro Eco BAARA')]
class MatchShow extends Component
{
    public CandidateMatch $match;
    public string $newStatus = '';
    public string $notes     = '';

    public function mount(CandidateMatch $match): void
    {
        $this->authorize('view', $match);
        $match->loadMissing('candidate.skills', 'offer.company', 'offer.skills', 'operator');
        $this->match     = $match;
        $this->newStatus = $match->status->value;
        $this->notes     = $match->notes ?? '';
    }

    public function updateStatus(): void
    {
        $this->authorize('update', $this->match);

        $statusEnum = MatchStatus::from($this->newStatus);
        $update = ['status' => $statusEnum, 'notes' => $this->notes ?: null];

        if ($statusEnum->isClosed()) {
            $update['closed_at'] = now();
        }

        $this->match->update($update);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->match)
            ->withProperties(['new_status' => $this->newStatus])
            ->log('match_status_updated');

        $this->dispatch('notify', type: 'success', message: 'Statut mis à jour.');
        $this->match->refresh();
    }

    public function render()
    {
        return view('livewire.matching.match-show', [
            'statuses' => MatchStatus::cases(),
        ]);
    }
}
