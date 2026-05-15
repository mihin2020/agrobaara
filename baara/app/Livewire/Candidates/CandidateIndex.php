<?php

namespace App\Livewire\Candidates;

use App\Enums\Gender;
use App\Models\Candidate;
use App\Models\ReferentialCommune;
use App\Models\ReferentialEducationLevel;
use App\Models\ReferentialSkill;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Candidats — Agro Eco BAARA')]
class CandidateIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $commune = '';

    #[Url]
    public string $skill = '';

    #[Url]
    public string $education = '';

    #[Url]
    public string $gender = '';

    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCommune(): void { $this->resetPage(); }
    public function updatedSkill(): void   { $this->resetPage(); }
    public function updatedEducation(): void { $this->resetPage(); }
    public function updatedGender(): void  { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->reset(['search', 'commune', 'skill', 'education', 'gender']);
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', Candidate::class);

        $user = Auth::user();

        $candidates = Candidate::with(['commune', 'skills'])
            ->when($user->isOperator(), fn($q) => $q->byOperator($user->id))
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->commune, fn($q) => $q->byCommune($this->commune))
            ->when($this->gender, fn($q) => $q->byGender($this->gender))
            ->when($this->education, fn($q) => $q->byEducation($this->education))
            ->when($this->skill, fn($q) => $q->bySkills([$this->skill]))
            ->latest()
            ->paginate($this->perPage);

        $communes   = ReferentialCommune::active()->get();
        $skills     = ReferentialSkill::active()->get();
        $educations = ReferentialEducationLevel::active()->get()->map(fn($e) => ['value' => $e->code, 'label' => $e->name]);
        $genders    = collect(Gender::cases())->map(fn($g) => ['value' => $g->value, 'label' => $g->label()]);

        return view('livewire.candidates.candidate-index', compact(
            'candidates', 'communes', 'skills', 'educations', 'genders'
        ));
    }
}
