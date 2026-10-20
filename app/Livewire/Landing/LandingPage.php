<?php

namespace App\Livewire\Landing;

use App\Models\LandingSection;
use App\Models\NewsItem;
use App\Models\Testimonial;
use App\Support\Feature;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.landing')]
#[Title('Agro Eco BAARA - Emploi agroécologique au Burkina Faso')]
class LandingPage extends Component
{
    public function render()
    {
        $sections = LandingSection::allOrdered()->keyBy('slug');

        $ribbonItems = Feature::enabled('news_ribbon')
            ? NewsItem::ribbon()->limit(8)->get(['id', 'title', 'slug', 'kind', 'published_at', 'starts_at', 'ends_at', 'status'])
            : collect();

        $latestNews = Feature::enabled('news_ribbon')
            ? NewsItem::published()
                ->latest('published_at')
                ->limit(6)
                ->get(['id', 'title', 'slug', 'kind', 'excerpt', 'published_at', 'status'])
            : collect();

        $dbTestimonials = Feature::enabled('testimonials')
            ? Testimonial::published()
                ->latest('published_at')
                ->limit(12)
                ->get(['id', 'author_name', 'role_label', 'body', 'published_at', 'status'])
            : collect();

        return view('livewire.landing.landing-page', compact(
            'sections',
            'ribbonItems',
            'latestNews',
            'dbTestimonials'
        ));
    }
}
