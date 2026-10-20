<?php

namespace App\Livewire\Public\News;

use App\Models\NewsItem;
use App\Support\Feature;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Actualités')]
class NewsIndex extends Component
{
    public function mount(): void
    {
        abort_unless(Feature::enabled('news_ribbon'), 404);
    }

    public function render()
    {
        return view('livewire.public.news.news-index', [
            'items' => NewsItem::published()->latest('published_at')->paginate(15),
        ]);
    }
}
