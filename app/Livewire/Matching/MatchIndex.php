<?php

namespace App\Livewire\Matching;

use App\Enums\MatchStatus;
use App\Exports\MatchesExport;
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
    public string $exportFrom = '';
    public string $exportTo = '';
    public string $exportStatus = '';

    public function updatedStatus(): void { $this->resetPage(); }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new MatchesExport(
                from: $this->exportFrom ?: null,
                to: $this->exportTo ?: null,
                status: $this->exportStatus ?: null,
            ),
            'matchings_' . date('Y-m-d') . '.xlsx'
        );
    }

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
