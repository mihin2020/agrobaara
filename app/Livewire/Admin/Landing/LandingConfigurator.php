<?php

namespace App\Livewire\Admin\Landing;

use App\Models\LandingSection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Configurateur Landing — Agro Eco BAARA')]
class LandingConfigurator extends Component
{
    use WithFileUploads;

    public ?string $editingSlug = null;
    public array   $formData    = [];
    public bool    $saved       = false;

    // Listes dynamiques par section
    public array $heroSlides     = [];
    public array $audCards       = [];
    public array $ceQueColumns   = [];
    public array $commentSteps   = [];
    public array $autresServices = [];
    public array $partnerItems   = [];
    public array $temoItems      = [];
    public array $mediaPhotos    = [];

    // ── Upload d'image ────────────────────────────────────────────────────────
    #[Validate('image|max:5120')]
    public $imageUploadFile = null;
    public string $imageUploadSlot = ''; // format: "hero.0" | "partner.2" | "media.5"

    #[Validate('image|max:5120')]
    public $guichetImageFile = null;

    public function mount(): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Ouverture / Fermeture de l'éditeur
    // ─────────────────────────────────────────────────────────────────────────

    public function editSection(string $slug): void
    {
        $section = LandingSection::forSlug($slug);
        if (!$section) return;

        $this->editingSlug = $slug;
        $content = $section->content ?? [];
        $this->formData = $content;

        // Initialise les sous-listes selon le type
        $this->heroSlides     = $content['slides']   ?? [];
        $this->audCards       = $content['cards']    ?? [];
        $this->ceQueColumns   = $content['columns']  ?? [];
        $this->commentSteps   = $content['steps']    ?? [];
        $this->autresServices = $content['services'] ?? [];
        $this->partnerItems   = $content['items']    ?? [];
        $this->temoItems      = $content['items']    ?? [];
        $this->mediaPhotos    = $content['photos']   ?? [];

        $this->saved = false;
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingSlug','formData','saved',
            'heroSlides','audCards','ceQueColumns','commentSteps',
            'autresServices','partnerItems','temoItems','mediaPhotos',
            'imageUploadFile','imageUploadSlot']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Upload d'image (héro, partenaires, médiathèque)
    // ─────────────────────────────────────────────────────────────────────────

    public function updatedImageUploadFile(): void
    {
        if (!$this->imageUploadFile || !$this->imageUploadSlot) return;

        $this->validateOnly('imageUploadFile');

        $destDir = $this->ensureUploadsDir();
        $ext     = $this->imageUploadFile->getClientOriginalExtension() ?: 'jpg';
        $name    = uniqid('upload_') . '.' . $ext;
        rename($this->imageUploadFile->getRealPath(), $destDir . DIRECTORY_SEPARATOR . $name);
        $url = '/images/uploads/' . $name;

        [$type, $index] = array_pad(explode('.', $this->imageUploadSlot, 2), 2, '0');
        $idx = (int) $index;

        match ($type) {
            'hero'    => $this->heroSlides[$idx]['image_url'] = $url,
            'partner' => $this->partnerItems[$idx]['logo']    = $url,
            'media'   => $this->mediaPhotos[$idx]['src']      = $url,
            'guichet' => $this->formData['image_url']         = $url,
            default   => null,
        };

        $this->imageUploadFile  = null;
        $this->imageUploadSlot  = '';
    }

    public function updatedGuichetImageFile(): void
    {
        if (!$this->guichetImageFile) return;

        $this->validateOnly('guichetImageFile');

        $destDir = $this->ensureUploadsDir();
        $ext     = $this->guichetImageFile->getClientOriginalExtension() ?: 'jpg';
        $name    = uniqid('upload_') . '.' . $ext;
        rename($this->guichetImageFile->getRealPath(), $destDir . DIRECTORY_SEPARATOR . $name);

        $this->formData['image_url'] = '/images/uploads/' . $name;
        $this->guichetImageFile = null;
        $this->dispatch('guichetUploaded', url: '/images/uploads/' . $name);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Sauvegarde
    // ─────────────────────────────────────────────────────────────────────────

    public function saveSection(): void
    {
        $section = LandingSection::forSlug($this->editingSlug);
        if (!$section) return;

        // Fusionne les sous-listes dans formData selon le slug
        match ($this->editingSlug) {
            'hero'                         => $this->formData['slides']   = $this->heroSlides,
            'audiences'                    => $this->formData['cards']    = $this->audCards,
            'ce_que_vous_pouvez_trouver'   => $this->formData['columns']  = $this->ceQueColumns,
            'comment'                      => $this->formData['steps']    = $this->commentSteps,
            'autres_services'              => $this->formData['services'] = $this->autresServices,
            'partenaires'                  => $this->formData['items']    = $this->partnerItems,
            'temoignages'                  => $this->formData['items']    = $this->temoItems,
            'mediatheque'                  => $this->formData['photos']   = $this->mediaPhotos,
            default                        => null,
        };

        $section->update(['content' => $this->formData]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($section)
            ->withProperties(['slug' => $this->editingSlug])
            ->log('landing_section_updated');

        $this->saved = true;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Visibilité & Ordre
    // ─────────────────────────────────────────────────────────────────────────

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
            $prev->save(); $curr->save();
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
            $next->save(); $curr->save();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers Hero Slides
    // ─────────────────────────────────────────────────────────────────────────

    public function addHeroSlide(): void
    {
        $this->heroSlides[] = [
            'title' => '', 'subtitle' => '', 'description' => '',
            'cta_primary_text' => '', 'cta_primary_link' => '#contact',
            'cta_secondary_text' => '', 'cta_secondary_link' => '',
            'image_url' => '',
        ];
    }

    public function removeHeroSlide(int $i): void
    {
        if (count($this->heroSlides) <= 1) return;
        array_splice($this->heroSlides, $i, 1);
        $this->heroSlides = array_values($this->heroSlides);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers Étapes Comment
    // ─────────────────────────────────────────────────────────────────────────

    public function addStep(): void
    {
        $n = count($this->commentSteps) + 1;
        $this->commentSteps[] = ['number' => (string)$n, 'title' => '', 'description' => ''];
    }

    public function removeStep(int $i): void
    {
        if (count($this->commentSteps) <= 1) return;
        array_splice($this->commentSteps, $i, 1);
        $this->commentSteps = array_values($this->commentSteps);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers Autres Services
    // ─────────────────────────────────────────────────────────────────────────

    public function addService(): void
    {
        $this->autresServices[] = ['icon' => 'star', 'label' => ''];
    }

    public function removeService(int $i): void
    {
        array_splice($this->autresServices, $i, 1);
        $this->autresServices = array_values($this->autresServices);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers Partenaires
    // ─────────────────────────────────────────────────────────────────────────

    public function addPartner(): void
    {
        $this->partnerItems[] = [
            'name' => '', 'logo' => '', 'description' => '',
            'website' => '#', 'social_label' => 'Site Web', 'social_icon' => 'language',
        ];
    }

    public function removePartner(int $i): void
    {
        array_splice($this->partnerItems, $i, 1);
        $this->partnerItems = array_values($this->partnerItems);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers Témoignages
    // ─────────────────────────────────────────────────────────────────────────

    public function addTemo(): void
    {
        $this->temoItems[] = ['name' => '', 'role' => '', 'avatar_color' => 'primary', 'text' => ''];
    }

    public function removeTemo(int $i): void
    {
        array_splice($this->temoItems, $i, 1);
        $this->temoItems = array_values($this->temoItems);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers Médiathèque
    // ─────────────────────────────────────────────────────────────────────────

    public function addPhoto(): void
    {
        $this->mediaPhotos[] = ['src' => '', 'alt' => '', 'category' => 'terrain'];
    }

    public function removePhoto(int $i): void
    {
        array_splice($this->mediaPhotos, $i, 1);
        $this->mediaPhotos = array_values($this->mediaPhotos);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers Ce que vous pouvez trouver (items dans chaque colonne)
    // ─────────────────────────────────────────────────────────────────────────

    public function addCeQueItem(int $colIndex): void
    {
        $this->ceQueColumns[$colIndex]['items'][] = '';
    }

    public function removeCeQueItem(int $colIndex, int $itemIndex): void
    {
        array_splice($this->ceQueColumns[$colIndex]['items'], $itemIndex, 1);
        $this->ceQueColumns[$colIndex]['items'] = array_values($this->ceQueColumns[$colIndex]['items']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers internes
    // ─────────────────────────────────────────────────────────────────────────

    private function ensureUploadsDir(): string
    {
        $dir = public_path('images') . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return realpath($dir);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────────────────

    public function render()
    {
        $sections = LandingSection::allOrdered();
        return view('livewire.admin.landing.landing-configurator', compact('sections'));
    }
}
