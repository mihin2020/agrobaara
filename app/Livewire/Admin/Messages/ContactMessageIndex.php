<?php

namespace App\Livewire\Admin\Messages;

use App\Models\ContactMessage;
use Livewire\Component;
use Livewire\WithPagination;

class ContactMessageIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // all, unread, read
    public ?string $selectedId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function selectMessage(string $id): void
    {
        $this->selectedId = $id;

        $message = ContactMessage::find($id);
        if ($message && !$message->is_read) {
            $message->markAsRead();
            cache()->forget('unread_messages_count');
        }
    }

    public function closeMessage(): void
    {
        $this->selectedId = null;
    }

    public function deleteMessage(string $id): void
    {
        ContactMessage::where('id', $id)->delete();
        cache()->forget('unread_messages_count');

        if ($this->selectedId === $id) {
            $this->selectedId = null;
        }
    }

    public function render()
    {
        $query = ContactMessage::query()->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('message', 'like', "%{$this->search}%");
            });
        }

        if ($this->filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($this->filter === 'read') {
            $query->where('is_read', true);
        }

        $messages = $query->paginate(20);
        $selected = $this->selectedId ? ContactMessage::find($this->selectedId) : null;
        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('livewire.admin.messages.contact-message-index', [
            'messages'    => $messages,
            'selected'    => $selected,
            'unreadCount' => $unreadCount,
        ])->layout('components.layouts.app', ['title' => 'Messages de contact']);
    }
}
