<?php

namespace App\Livewire\Public\News;

use App\Models\NewsItem;
use App\Support\Feature;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Actualité')]
class NewsShow extends Component
{
    public NewsItem $item;

    public function mount(string $slug): void
    {
        abort_unless(Feature::enabled('news_ribbon'), 404);
        $this->item = NewsItem::published()->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.public.news.news-show');
    }
}
