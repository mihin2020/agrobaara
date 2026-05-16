<?php

namespace App\Livewire\Matching;

use App\Enums\MatchStatus;
use App\Models\CandidateMatch;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Matching — Agro Eco BAARA')]
class MatchIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function updatedStatus(): void { $this->resetPage(); }

    public function updateStatus(string $matchId, string $newStatus): void
    {
        $match = CandidateMatch::findOrFail($matchId);
        $this->authorize('update', $match);

        $statusEnum = MatchStatus::from($newStatus);
        $match->update(['status' => $statusEnum]);

        if ($statusEnum->isClosed()) {
            $match->update(['closed_at' => now()]);
        }

        activity()
            ->causedBy(Auth::user())
            ->performedOn($match)
            ->withProperties(['new_status' => $newStatus])
            ->log('match_status_updated');
    }

    public function render()
    {
        $this->authorize('viewAny', CandidateMatch::class);

        $user = Auth::user();

        $matches = CandidateMatch::with(['candidate', 'offer.company', 'operator'])
            ->when($user->isOperator(), fn($q) => $q->byOperator($user->id))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);

        $statuses = MatchStatus::cases();

        return view('livewire.matching.match-index', compact('matches', 'statuses'));
    }
}
