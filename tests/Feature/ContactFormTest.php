<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Landing\ContactForm;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_can_submit_valid_message(): void
    {
        Livewire::test(ContactForm::class)
            ->set('full_name', 'Jean Dupont')
            ->set('email', 'jean@example.com')
            ->set('phone', '+226 70 00 00 00')
            ->set('message', 'Bonjour, je souhaite avoir des informations sur vos services.')
            ->set('rgpd_consent', true)
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('contact_messages', [
            'full_name' => 'Jean Dupont',
            'email' => 'jean@example.com',
        ]);
    }

    public function test_contact_form_requires_valid_email(): void
    {
        Livewire::test(ContactForm::class)
            ->set('full_name', 'Jean Dupont')
            ->set('email', 'invalid-email')
            ->set('message', 'Un message suffisamment long pour passer la validation.')
            ->set('rgpd_consent', true)
            ->call('submit')
            ->assertHasErrors('email');
    }

    public function test_contact_form_requires_rgpd_consent(): void
    {
        Livewire::test(ContactForm::class)
            ->set('full_name', 'Jean Dupont')
            ->set('email', 'jean@example.com')
            ->set('message', 'Un message suffisamment long pour passer la validation.')
            ->set('rgpd_consent', false)
            ->call('submit')
            ->assertHasErrors('rgpd_consent');
    }

    public function test_honeypot_blocks_spam(): void
    {
        Livewire::test(ContactForm::class)
            ->set('full_name', 'Spam Bot')
            ->set('email', 'bot@spam.com')
            ->set('message', 'This is spam with enough characters to pass.')
            ->set('rgpd_consent', true)
            ->set('honeypot', 'gotcha')
            ->call('submit')
            ->assertSet('submitted', false);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_rate_limiting_blocks_excessive_submissions(): void
    {
        $component = Livewire::test(ContactForm::class);

        for ($i = 0; $i < 3; $i++) {
            Livewire::test(ContactForm::class)
                ->set('full_name', "User $i")
                ->set('email', "user$i@example.com")
                ->set('message', "Message numéro $i qui est assez long pour valider.")
                ->set('rgpd_consent', true)
                ->call('submit');
        }

        // Fourth attempt should be blocked
        Livewire::test(ContactForm::class)
            ->set('full_name', 'Blocked User')
            ->set('email', 'blocked@example.com')
            ->set('message', 'Ce message devrait être bloqué par le rate limit.')
            ->set('rgpd_consent', true)
            ->call('submit')
            ->assertHasErrors('message');
    }
}
