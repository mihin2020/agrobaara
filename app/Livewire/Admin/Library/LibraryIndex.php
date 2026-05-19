<?php

namespace App\Livewire\Admin\Library;

use App\Models\LibraryDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Bibliothèque — Agro Eco BAARA')]
class LibraryIndex extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    // Modal ajout / modification
    public bool    $showModal        = false;
    public ?string $editingId        = null;
    public string  $type             = 'file';
    public string  $title            = '';
    public string  $description      = '';
    public         $file             = null;
    public string  $external_url     = '';
    public string  $currentFileName  = '';
    public string  $currentFilePath  = '';
    public string  $currentFileSize  = '';

    // Suppression
    public bool    $confirmingDelete = false;
    public ?string $deleteId         = null;

    public function updatedSearch(): void { $this->resetPage(); }

    public function openModal(): void
    {
        $this->reset(['editingId', 'type', 'title', 'description', 'file', 'external_url']);
        $this->type      = 'file';
        $this->showModal = true;
        $this->resetValidation();
    }

    public function openEditModal(string $id): void
    {
        $doc = LibraryDocument::findOrFail($id);

        $this->editingId       = $id;
        $this->type            = $doc->type;
        $this->title           = $doc->title;
        $this->description     = $doc->description ?? '';
        $this->external_url    = $doc->external_url ?? '';
        $this->file            = null;
        $this->currentFileName = $doc->original_name ?? '';
        $this->currentFilePath = $doc->file_path ?? '';
        $this->currentFileSize = $doc->fileSizeForHumans();
        $this->showModal       = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $isEdit = (bool) $this->editingId;

        $rules = [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type'        => 'required|in:file,link',
        ];

        if ($this->type === 'file') {
            $rules['file'] = $isEdit ? 'nullable|file|max:10240' : 'required|file|max:10240';
        } else {
            $rules['external_url'] = 'required|url|max:500';
        }

        $this->validate($rules, [
            'title.required'        => 'Le titre est obligatoire.',
            'file.required'         => 'Veuillez sélectionner un fichier.',
            'file.max'              => 'Le fichier ne doit pas dépasser 10 Mo.',
            'external_url.required' => 'Le lien est obligatoire.',
            'external_url.url'      => 'Le lien n\'est pas valide.',
        ]);

        if ($isEdit) {
            $doc = LibraryDocument::findOrFail($this->editingId);

            $data = [
                'title'       => $this->title,
                'description' => $this->description ?: null,
            ];

            if ($this->type === 'file') {
                if ($this->file) {
                    if ($doc->file_path) Storage::disk('public')->delete($doc->file_path);
                    $data['file_path']     = $this->file->store('library', 'public');
                    $data['original_name'] = $this->file->getClientOriginalName();
                    $data['file_size']     = $this->file->getSize();
                    $data['mime_type']     = $this->file->getMimeType();
                }
            } else {
                $data['external_url'] = $this->external_url;
                // Si on passe de fichier à lien, supprimer l'ancien fichier
                if ($doc->type === 'file' && $doc->file_path) {
                    Storage::disk('public')->delete($doc->file_path);
                    $data['file_path'] = $data['original_name'] = $data['file_size'] = $data['mime_type'] = null;
                }
            }

            $doc->update($data);
            $message = 'Document mis à jour avec succès.';
        } else {
            $data = [
                'title'       => $this->title,
                'description' => $this->description ?: null,
                'type'        => $this->type,
                'created_by'  => Auth::id(),
            ];

            if ($this->type === 'file') {
                $data['file_path']     = $this->file->store('library', 'public');
                $data['original_name'] = $this->file->getClientOriginalName();
                $data['file_size']     = $this->file->getSize();
                $data['mime_type']     = $this->file->getMimeType();
            } else {
                $data['external_url'] = $this->external_url;
            }

            LibraryDocument::create($data);
            $message = 'Document ajouté avec succès.';
        }

        $this->showModal = false;
        $this->reset(['editingId', 'title', 'description', 'file', 'external_url']);
        session()->flash('success', $message);
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId         = $id;
        $this->confirmingDelete = true;
    }

    public function deleteDocument(): void
    {
        if (!$this->deleteId) return;

        $doc = LibraryDocument::findOrFail($this->deleteId);

        if ($doc->file_path) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        $this->reset(['confirmingDelete', 'deleteId']);
        session()->flash('success', 'Document supprimé.');
    }

    public function render()
    {
        $documents = LibraryDocument::with('creator')
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(12);

        return view('livewire.admin.library.library-index', compact('documents'));
    }
}
