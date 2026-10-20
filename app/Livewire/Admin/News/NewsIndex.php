<?php

namespace App\Livewire\Admin\News;

use App\Models\NewsItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Actualités')]
class NewsIndex extends Component
{
    use WithPagination;

    public string $title = '';

    public string $kind = 'actu';

    public string $excerpt = '';

    public string $body = '';

    public string $starts_at = '';

    public string $ends_at = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->hasPermission('news.view'), 403);
    }

    public function save(): void
    {
        abort_unless(Auth::user()->hasPermission('news.create'), 403);
        $this->validate([
            'title'     => 'required|string|max:200',
            'kind'      => 'required|in:actu,annonce,evenement',
            'excerpt'   => 'nullable|string|max:500',
            'body'      => 'nullable|string',
            'starts_at' => 'nullable|date',
            'ends_at'   => 'nullable|date|after_or_equal:starts_at',
        ]);

        NewsItem::create([
            'title'      => $this->title,
            'slug'       => Str::slug($this->title).'-'.Str::lower(Str::random(4)),
            'kind'       => $this->kind,
            'excerpt'    => $this->excerpt ?: null,
            'body'       => $this->body ?: null,
            'starts_at'  => $this->starts_at ?: null,
            'ends_at'    => $this->ends_at ?: null,
            'status'     => 'draft',
            'created_by' => Auth::id(),
        ]);

        $this->reset('title', 'excerpt', 'body', 'starts_at', 'ends_at');
        $this->kind = 'actu';
        $this->resetPage();
        session()->flash('success', 'Brouillon créé.');
    }

    public function publish(string $id): void
    {
        abort_unless(Auth::user()->hasPermission('news.publish'), 403);
        NewsItem::findOrFail($id)->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);
        session()->flash('success', 'Publié.');
    }

    public function archive(string $id): void
    {
        abort_unless(Auth::user()->hasPermission('news.update'), 403);
        NewsItem::findOrFail($id)->update(['status' => 'archived']);
        session()->flash('success', 'Archivé.');
    }

    public function render()
    {
        return view('livewire.admin.news.news-index', [
            'items' => NewsItem::with('author:id,first_name,last_name')->latest()->paginate(20),
        ]);
    }
}
