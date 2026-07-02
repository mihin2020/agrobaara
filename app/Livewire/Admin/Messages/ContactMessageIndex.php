<?php

namespace App\Livewire\Admin\Messages;

use App\Mail\ContactReply;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class ContactMessageIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // all, unread, read
    public ?string $selectedId = null;
    public string $replyMessage = '';

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
        $this->reset('replyMessage');
        $this->resetValidation('replyMessage');

        $message = ContactMessage::find($id);
        if ($message && !$message->is_read) {
            $message->markAsRead();
            cache()->forget('unread_messages_count');
        }
    }

    public function closeMessage(): void
    {
        $this->selectedId = null;
        $this->reset('replyMessage');
        $this->resetValidation('replyMessage');
    }

    public function sendReply(): void
    {
        if (!$this->selectedId) {
            return;
        }

        $this->validate([
            'replyMessage' => 'required|string|min:5|max:5000',
        ], [
            'replyMessage.required' => 'La réponse est obligatoire.',
            'replyMessage.min'      => 'La réponse doit contenir au moins 5 caractères.',
            'replyMessage.max'      => 'La réponse ne peut pas dépasser 5000 caractères.',
        ]);

        $message = ContactMessage::findOrFail($this->selectedId);

        Mail::to($message->email)->queue(new ContactReply($message, $this->replyMessage));

        $message->markAsReplied($this->replyMessage, Auth::id());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($message)
            ->log('contact_message_replied');

        $this->reset('replyMessage');
        session()->flash('reply_success', "Réponse envoyée à {$message->email}.");
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
