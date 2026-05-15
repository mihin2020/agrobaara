<?php

namespace App\Livewire\Admin\Audit;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('components.layouts.app')]
#[Title("Journal d'audit — Agro Eco BAARA")]
class AuditLog extends Component
{
    use WithPagination;

    #[Url]
    public string $search    = '';
    #[Url]
    public string $event     = '';
    #[Url]
    public string $dateFrom  = '';
    #[Url]
    public string $dateTo    = '';

    public function mount(): void
    {
        if (!Auth::user()->hasPermission('audit.view')) {
            abort(403);
        }
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function render()
    {
        $activities = Activity::with('causer', 'subject')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                  ->orWhereHas('causer', fn($q) => $q->where('email', 'like', "%{$this->search}%"));
            }))
            ->when($this->event, fn($q) => $q->where('description', $this->event))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(5);

        $events = Activity::distinct()->pluck('description')->sort()->values();

        return view('livewire.admin.audit.audit-log', compact('activities', 'events'));
    }
}
