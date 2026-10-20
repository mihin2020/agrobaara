<?php

namespace App\Livewire\Admin\Testimonials;

use App\Models\Testimonial;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Témoignages')]
class TestimonialIndex extends Component
{
    use WithPagination;

    public function mount(): void
    {
        abort_unless(Auth::user()->hasPermission('testimonials.view'), 403);
    }

    public function approve(string $id): void
    {
        abort_unless(Auth::user()->hasPermission('testimonials.approve'), 403);
        Testimonial::findOrFail($id)->publish(Auth::user());
        session()->flash('success', 'Témoignage publié.');
    }

    public function unpublish(string $id): void
    {
        abort_unless(Auth::user()->hasPermission('testimonials.unpublish'), 403);
        Testimonial::findOrFail($id)->unpublish(Auth::user());
        session()->flash('success', 'Témoignage dépublié.');
    }

    public function render()
    {
        return view('livewire.admin.testimonials.testimonial-index', [
            'items' => Testimonial::with('candidate:id,first_name,last_name,reference')
                ->latest()
                ->paginate(20),
        ]);
    }
}
