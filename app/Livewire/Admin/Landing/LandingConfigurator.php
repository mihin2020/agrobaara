<?php

namespace App\Livewire\Admin\Landing;

use App\Models\LandingSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
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
    public string  $saveNotice  = '';

    // Listes dynamiques par section
    public array $heroSlides     = [];
    public array $audCards       = [];
    public array $ceQueColumns   = [];
    public array $commentSteps   = [];
    public array $autresServices = [];
    public array $partnerItems   = [];
    public array $temoItems      = [];
    public array $mediaPhotos     = [];
    public array $mediaCategories = [];
    public string $mediaTab       = 'photo';
    public ?int   $editingMediaIndex = null;
    public string $pendingMediaType  = 'image';
    public bool   $categoriesPanelOpen = true;
    public bool   $videoLinkFormOpen   = false;
    public string $videoLinkInput      = '';

    // ── Upload d'image / média ───────────────────────────────────────────────
    public $imageUploadFile = null;
    public $photoUploadFile  = null;
    public $videoUploadFile  = null;
    public string $imageUploadSlot = '';
    public string $mediaUploadSlot  = '';

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
        $this->mediaPhotos     = $content['photos']     ?? [];
        $this->mediaCategories = $content['categories'] ?? self::defaultMediaCategories();
        $this->mediaTab        = 'photo';
        $this->editingMediaIndex = null;
        $this->categoriesPanelOpen = true;

        $this->saved = false;
        $this->saveNotice = '';
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingSlug','formData','saved','saveNotice',
            'heroSlides','audCards','ceQueColumns','commentSteps',
            'autresServices','partnerItems','temoItems','mediaPhotos','mediaCategories',
            'mediaTab','editingMediaIndex','categoriesPanelOpen','videoLinkFormOpen','videoLinkInput',
            'imageUploadFile','imageUploadSlot','photoUploadFile','videoUploadFile','mediaUploadSlot']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Upload d'image (héro, partenaires, médiathèque)
    // ─────────────────────────────────────────────────────────────────────────

    public function updatedImageUploadFile(): void
    {
        if (!$this->imageUploadFile || !$this->imageUploadSlot) return;

        $this->validate([
            'imageUploadFile' => 'required|image|max:10240',
        ], [
            'imageUploadFile.image' => 'Le fichier doit être une image.',
            'imageUploadFile.max'   => 'L\'image ne doit pas dépasser 10 Mo.',
        ]);

        try {
            $url = $this->storePublicUpload($this->imageUploadFile);
            $this->applyUploadToSlot($this->imageUploadSlot, $url);
            $this->imageUploadFile = null;
            $this->imageUploadSlot = '';
            session()->flash('upload_success', 'Fichier téléversé avec succès.');
        } catch (\Throwable $e) {
            $this->imageUploadFile = null;
            $this->imageUploadSlot = '';
            session()->flash('upload_error', 'Échec du téléversement : ' . $e->getMessage());
        }
    }

    public function updatedPhotoUploadFile(): void
    {
        $this->processMediaUpload($this->photoUploadFile, 'image');
        $this->photoUploadFile = null;
    }

    public function updatedVideoUploadFile(): void
    {
        $this->processMediaUpload($this->videoUploadFile, 'video');
        $this->videoUploadFile = null;
    }

    private function processMediaUpload($file, string $type): void
    {
        if (!$file) return;

        if (!$this->mediaUploadSlot) {
            $this->mediaUploadSlot = 'media.new';
        }

        Validator::make(
            ['file' => $file],
            ['file' => $type === 'video'
                ? ['required', 'file', 'max:204800', function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension() ?: '');
                    $allowed = ['mp4', 'webm', 'mov', 'm4v', 'avi', 'mkv'];
                    if (! in_array($ext, $allowed, true)) {
                        $fail('Formats vidéo acceptés : mp4, webm, mov, m4v, avi, mkv.');
                    }
                }]
                : 'required|image|max:10240'],
            [
                'file.image' => 'Le fichier doit être une image.',
                'file.max'   => $type === 'video' ? 'La vidéo ne doit pas dépasser 200 Mo.' : 'L\'image ne doit pas dépasser 10 Mo.',
            ]
        )->validate();

        try {
            $url = $this->storePublicUpload($file);
            [$slotType, $index] = array_pad(explode('.', $this->mediaUploadSlot, 2), 2, '0');
            $idx = (int) $index;

            if ($slotType === 'media') {
                if ($index === 'new') {
                    $this->mediaPhotos[] = [
                        'type'     => $type,
                        'src'      => $url,
                        'source'   => 'upload',
                        'alt'      => '',
                        'category' => $this->defaultMediaCategoryKey(),
                    ];
                    $this->editingMediaIndex = count($this->mediaPhotos) - 1;
                } elseif (isset($this->mediaPhotos[$idx])) {
                    $this->mediaPhotos[$idx]['src']    = $url;
                    $this->mediaPhotos[$idx]['type']   = $type;
                    $this->mediaPhotos[$idx]['source'] = 'upload';
                    $this->editingMediaIndex = $idx;
                }
                $this->mediaTab = $type === 'video' ? 'video' : 'photo';
                $this->persistSection('mediatheque');
            }

            $this->mediaUploadSlot = '';
            session()->flash('upload_success', $type === 'video'
                ? 'Vidéo importée avec succès.'
                : 'Photo importée avec succès.');
        } catch (\Throwable $e) {
            $this->mediaUploadSlot = '';
            session()->flash('upload_error', 'Échec du téléversement : ' . $e->getMessage());
        }
    }

    public function updatedGuichetImageFile(): void
    {
        if (!$this->guichetImageFile) return;

        $this->validate([
            'guichetImageFile' => 'required|image|max:10240',
        ]);

        try {
            $url = $this->storePublicUpload($this->guichetImageFile);
            $this->formData['image_url'] = $url;
            $this->guichetImageFile = null;
            $this->dispatch('guichetUploaded', url: $url);
        } catch (\Throwable $e) {
            $this->guichetImageFile = null;
            session()->flash('upload_error', 'Échec du téléversement guichet : ' . $e->getMessage());
        }
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
            'mediatheque'                  => $this->syncMediathequeFormData(),
            default                        => null,
        };

        $section->update(['content' => $this->formData]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($section)
            ->withProperties(['slug' => $this->editingSlug])
            ->log('landing_section_updated');

        $notice = match ($this->editingSlug) {
            'mediatheque' => 'Médiathèque enregistrée avec succès.',
            default       => 'Section enregistrée avec succès.',
        };

        $this->cancelEdit();
        $this->saveNotice = $notice;
        session()->flash('upload_success', $notice);
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
        $this->startMediaUpload('image');
    }

    public function addVideo(): void
    {
        $this->videoLinkFormOpen = false;
        $this->startMediaUpload('video');
    }

    public function openVideoLinkForm(): void
    {
        $this->videoLinkFormOpen = true;
        $this->videoLinkInput    = '';
        $this->mediaTab          = 'video';
        $this->editingMediaIndex = null;
    }

    public function cancelVideoLinkForm(): void
    {
        $this->videoLinkFormOpen = false;
        $this->videoLinkInput    = '';
        $this->resetValidation(['videoLinkInput']);
    }

    public function addVideoFromLink(): void
    {
        $this->validate([
            'videoLinkInput' => 'required|url|max:500',
        ], [
            'videoLinkInput.required' => 'Collez un lien vidéo (YouTube, Vimeo ou MP4).',
            'videoLinkInput.url'      => 'Le lien n\'est pas valide.',
        ]);

        $this->mediaPhotos[] = [
            'type'     => 'video',
            'src'      => trim($this->videoLinkInput),
            'source'   => 'url',
            'alt'      => '',
            'category' => $this->defaultMediaCategoryKey(),
        ];

        $this->editingMediaIndex = count($this->mediaPhotos) - 1;
        $this->mediaTab          = 'video';
        $this->videoLinkFormOpen = false;
        $this->videoLinkInput    = '';
        $this->persistSection('mediatheque');

        session()->flash('upload_success', 'Vidéo ajoutée via lien.');
    }

    public function startMediaUpload(string $type): void
    {
        $this->pendingMediaType  = $type;
        $this->mediaUploadSlot   = 'media.new';
        $this->mediaTab          = $type === 'video' ? 'video' : 'photo';
        $this->editingMediaIndex = null;
        if ($type === 'video') {
            $this->videoLinkFormOpen = false;
        }

        $this->openMediaFilePicker($type);
    }

    private function openMediaFilePicker(string $type): void
    {
        $inputId = $type === 'video' ? 'global-video-upload' : 'global-photo-upload';
        $this->js(<<<JS
            setTimeout(function () {
                var input = document.getElementById('{$inputId}');
                if (input) {
                    input.value = '';
                    input.click();
                }
            }, 50);
        JS);
    }

    public function editMediaItem(int $i): void
    {
        $this->editingMediaIndex = $i;
        $type = $this->mediaPhotos[$i]['type'] ?? 'image';
        $this->mediaTab = ($type === 'video') ? 'video' : 'photo';
    }

    public function closeMediaEdit(): void
    {
        $this->editingMediaIndex = null;
    }

    public function replaceMediaFile(int $i, string $type): void
    {
        if (!isset($this->mediaPhotos[$i])) return;
        $this->mediaUploadSlot = "media.{$i}";
        $this->openMediaFilePicker($type === 'video' ? 'video' : 'photo');
    }

    public function addMediaCategory(): void
    {
        $n = count($this->mediaCategories) + 1;
        $this->mediaCategories[] = [
            'key'   => 'categorie_' . $n,
            'label' => 'Nouvelle catégorie',
        ];
        $this->categoriesPanelOpen = true;
    }

    public function toggleCategoriesPanel(): void
    {
        $this->categoriesPanelOpen = !$this->categoriesPanelOpen;
    }

    public function removeMediaCategory(int $i): void
    {
        if (count($this->mediaCategories) <= 1) return;

        $removedKey = $this->mediaCategories[$i]['key'] ?? '';
        array_splice($this->mediaCategories, $i, 1);
        $this->mediaCategories = array_values($this->mediaCategories);

        $fallback = $this->defaultMediaCategoryKey();
        foreach ($this->mediaPhotos as $idx => $photo) {
            if (($photo['category'] ?? '') === $removedKey) {
                $this->mediaPhotos[$idx]['category'] = $fallback;
            }
        }
    }

    public function removePhoto(int $i): void
    {
        array_splice($this->mediaPhotos, $i, 1);
        $this->mediaPhotos = array_values($this->mediaPhotos);

        if ($this->editingMediaIndex === $i) {
            $this->editingMediaIndex = null;
        } elseif ($this->editingMediaIndex !== null && $this->editingMediaIndex > $i) {
            $this->editingMediaIndex--;
        }
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
        return realpath($dir) ?: $dir;
    }

    private function storePublicUpload($file): string
    {
        $destDir  = $this->ensureUploadsDir();
        $ext      = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $name     = uniqid('upload_') . '.' . $ext;
        $destPath = $destDir . DIRECTORY_SEPARATOR . $name;
        $source   = $file->getRealPath();

        if (! $source || ! is_readable($source)) {
            throw new \RuntimeException('Fichier temporaire illisible.');
        }

        if (@rename($source, $destPath)) {
            return '/images/uploads/' . $name;
        }

        $in = fopen($source, 'rb');
        if ($in === false) {
            throw new \RuntimeException('Impossible de lire le fichier source.');
        }
        $out = fopen($destPath, 'wb');
        if ($out === false) {
            fclose($in);
            throw new \RuntimeException('Impossible d\'écrire dans le dossier public.');
        }
        stream_copy_to_stream($in, $out);
        fclose($in);
        fclose($out);

        return '/images/uploads/' . $name;
    }

    private function applyUploadToSlot(string $slot, string $url): void
    {
        [$type, $index] = array_pad(explode('.', $slot, 2), 2, '0');
        $idx = (int) $index;

        match ($type) {
            'hero'        => $this->heroSlides[$idx]['image_url'] = $url,
            'partner'     => $this->partnerItems[$idx]['logo']    = $url,
            'media'       => $this->mediaPhotos[$idx]['src']      = $url,
            'guichet'     => $this->formData['image_url']         = $url,
            'header_logo' => $this->formData['logo_url']          = $url,
            'projet'      => $this->formData['image_url']         = $url,
            default       => null,
        };

        if ($type === 'media' && isset($this->mediaPhotos[$idx])) {
            $this->mediaPhotos[$idx]['type'] = 'image';
            $this->persistSection('mediatheque');
        }
    }

    private function syncMediathequeFormData(): void
    {
        $this->normalizeMediaCategoryKeys();
        $this->formData['photos']     = $this->mediaPhotos;
        $this->formData['categories'] = $this->mediaCategories;
    }

    private function normalizeMediaCategoryKeys(): void
    {
        $used = [];
        foreach ($this->mediaCategories as $i => &$cat) {
            $label = trim($cat['label'] ?? '');
            $key   = trim($cat['key'] ?? '');

            if ($label !== '' && ($key === '' || str_starts_with($key, 'categorie_'))) {
                $key = Str::slug($label, '_');
            }

            if ($key === '') {
                $key = 'categorie_' . ($i + 1);
            }

            $base = $key;
            $n    = 2;
            while (in_array($key, $used, true)) {
                $key = $base . '_' . $n++;
            }

            $cat['key']   = $key;
            $cat['label'] = $label !== '' ? $label : ucfirst(str_replace('_', ' ', $key));
            $used[]       = $key;
        }
        unset($cat);
    }

    private function defaultMediaCategoryKey(): string
    {
        return $this->mediaCategories[0]['key'] ?? 'terrain';
    }

    public static function defaultMediaCategories(): array
    {
        return [
            ['key' => 'terrain',   'label' => 'Terrain'],
            ['key' => 'formation', 'label' => 'Formation'],
            ['key' => 'evenement', 'label' => 'Événement'],
        ];
    }

    private function persistSection(string $slug): void
    {
        if ($this->editingSlug !== $slug) return;

        $section = LandingSection::forSlug($slug);
        if (!$section) return;

        match ($slug) {
            'mediatheque' => $this->syncMediathequeFormData(),
            default       => null,
        };

        $section->update(['content' => $this->formData]);
        $this->saved = true;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────────────────

    public function render()
    {
        $sections = LandingSection::allOrdered();
        $mediaPhotoCount = collect($this->mediaPhotos)->where(fn ($m) => ($m['type'] ?? 'image') !== 'video')->count();
        $mediaVideoCount = collect($this->mediaPhotos)->where(fn ($m) => ($m['type'] ?? 'image') === 'video')->count();

        return view('livewire.admin.landing.landing-configurator', compact('sections', 'mediaPhotoCount', 'mediaVideoCount'));
    }
}
