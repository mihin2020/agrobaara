<?php

namespace App\Livewire\Admin\Landing;

use App\Models\LandingSection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Configurateur Landing — Agro Eco BAARA')]
class LandingConfigurator extends Component
{
    public ?string $editingSlug = null;
    public array   $formData    = [];

    // Partenaires (liste éditable)
    public array  $partnerItems = [];

    // Pour qui — cartes
    public array  $pourQuiCards = [];

    // Étapes — comment ça marche
    public array  $steps = [];

    public bool $saved = false;

    public function mount(): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
    }

    public function editSection(string $slug): void
    {
        $section = LandingSection::forSlug($slug);

        if (!$section) {
            return;
        }

        $this->editingSlug = $slug;
        $content = $section->content ?? [];

        $this->formData = $content;

        // Initialise les sous-listes selon le type
        $this->partnerItems  = $content['items'] ?? [];
        $this->pourQuiCards  = $content['cards'] ?? [];
        $this->steps         = $content['steps'] ?? [];

        $this->saved = false;
    }

    public function cancelEdit(): void
    {
        $this->editingSlug  = null;
        $this->formData     = [];
        $this->partnerItems = [];
        $this->pourQuiCards = [];
        $this->steps        = [];
        $this->saved        = false;
    }

    public function saveSection(): void
    {
        $section = LandingSection::forSlug($this->editingSlug);

        if (!$section) {
            return;
        }

        // Fusionne les sous-listes dans formData
        if ($this->editingSlug === 'partenaires') {
            // Nettoie les items vides
            $this->formData['items'] = array_values(
                array_filter($this->partnerItems, fn($v) => trim($v) !== '')
            );
        }

        if ($this->editingSlug === 'pour_qui') {
            $this->formData['cards'] = $this->pourQuiCards;
        }

        if ($this->editingSlug === 'comment') {
            $this->formData['steps'] = $this->steps;
        }

        $section->update(['content' => $this->formData]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($section)
            ->withProperties(['slug' => $this->editingSlug])
            ->log('landing_section_updated');

        $this->saved = true;
    }

    public function toggleSection(string $id): void
    {
        $section = LandingSection::findOrFail($id);

        if ($section->always_visible) {
            session()->flash('error', 'Cette section est toujours visible et ne peut pas être masquée.');
            return;
        }

        $section->update(['is_active' => !$section->is_active]);
    }

    public function moveSectionUp(string $id): void
    {
        $sections = LandingSection::orderBy('order_index')->get();
        $index    = $sections->search(fn($s) => $s->id === $id);

        if ($index > 0) {
            $prev = $sections[$index - 1];
            $curr = $sections[$index];

            [$prev->order_index, $curr->order_index] = [$curr->order_index, $prev->order_index];
            $prev->save();
            $curr->save();
        }
    }

    public function moveSectionDown(string $id): void
    {
        $sections = LandingSection::orderBy('order_index')->get();
        $index    = $sections->search(fn($s) => $s->id === $id);

        if ($index < $sections->count() - 1) {
            $next = $sections[$index + 1];
            $curr = $sections[$index];

            [$next->order_index, $curr->order_index] = [$curr->order_index, $next->order_index];
            $next->save();
            $curr->save();
        }
    }

    // Helpers pour la liste de partenaires
    public function addPartner(): void
    {
        $this->partnerItems[] = '';
    }

    public function removePartner(int $index): void
    {
        array_splice($this->partnerItems, $index, 1);
        $this->partnerItems = array_values($this->partnerItems);
    }

    // Helpers pour les étapes "Comment ça marche"
    public function addStep(): void
    {
        $this->steps[] = ['title' => '', 'description' => ''];
    }

    public function removeStep(int $index): void
    {
        array_splice($this->steps, $index, 1);
        $this->steps = array_values($this->steps);
    }

    public function render()
    {
        $sections = LandingSection::allOrdered();

        return view('livewire.admin.landing.landing-configurator', compact('sections'));
    }
}