<?php

namespace Tests\Feature;

use App\Livewire\Admin\Messages\ContactMessageIndex;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactMessagesAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_messages_page_requires_auth(): void
    {
        $response = $this->get('/admin/messages');
        $response->assertRedirect('/connexion');
    }

    public function test_messages_page_is_accessible_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/messages');
        $response->assertStatus(200);
    }

    public function test_can_select_and_mark_message_as_read(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $message = ContactMessage::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test subject',
            'message' => 'Test message content long enough',
            'rgpd_consent' => true,
            'ip_address' => '127.0.0.1',
        ]);

        $message->refresh();
        $this->assertFalse($message->is_read);

        Livewire::test(ContactMessageIndex::class)
            ->call('selectMessage', $message->id);

        $message->refresh();
        $this->assertTrue($message->is_read);
    }

    public function test_can_delete_message(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $message = ContactMessage::create([
            'full_name' => 'Delete Me',
            'email' => 'delete@example.com',
            'subject' => 'Delete subject',
            'message' => 'This message should be deleted from db',
            'rgpd_consent' => true,
            'ip_address' => '127.0.0.1',
        ]);

        Livewire::test(ContactMessageIndex::class)
            ->call('deleteMessage', $message->id);

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_cache_is_cleared_on_read(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $message = ContactMessage::create([
            'full_name' => 'Cache Test',
            'email' => 'cache@example.com',
            'subject' => 'Cache subject',
            'message' => 'Testing cache invalidation on read',
            'rgpd_consent' => true,
            'ip_address' => '127.0.0.1',
        ]);

        // Seed cache
        cache()->put('unread_messages_count', 5, 60);

        Livewire::test(ContactMessageIndex::class)
            ->call('selectMessage', $message->id);

        $this->assertNull(cache()->get('unread_messages_count'));
    }
}
