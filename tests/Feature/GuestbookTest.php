<?php

namespace Tests\Feature;

use App\Models\GuestbookEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GuestbookTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_guestbook_page_renders_with_an_empty_state(): void
    {
        $response = $this->get(route('guestbook.index'));

        $response->assertOk();
        $response->assertSee('Sign the guestbook');
        $response->assertSee('No entries yet. Be the first to sign!');
    }

    public function test_the_health_check_endpoint_responds(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_existing_entries_are_listed_newest_first(): void
    {
        GuestbookEntry::factory()->create([
            'name' => 'Older Signer',
            'created_at' => Carbon::now()->subDay(),
        ]);
        GuestbookEntry::factory()->create([
            'name' => 'Newer Signer',
            'created_at' => Carbon::now(),
        ]);

        $response = $this->get(route('guestbook.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Newer Signer', 'Older Signer']);
        $response->assertDontSee('No entries yet.');
    }

    public function test_only_the_most_recent_entries_are_displayed(): void
    {
        $oldest = GuestbookEntry::factory()->create([
            'name' => 'The Very First Signer',
            'created_at' => Carbon::now()->subYear(),
        ]);
        GuestbookEntry::factory(GuestbookEntry::MAX_DISPLAYED)->create();

        $response = $this->get(route('guestbook.index'));

        $response->assertOk();
        $response->assertDontSee($oldest->name);
    }

    public function test_soft_deleted_entries_are_not_displayed(): void
    {
        $entry = GuestbookEntry::factory()->create(['name' => 'Removed Signer']);
        $entry->delete();

        $this->get(route('guestbook.index'))->assertDontSee('Removed Signer');
    }

    public function test_a_visitor_can_sign_the_guestbook(): void
    {
        $response = $this->post(route('guestbook.store'), [
            'name' => 'Ada Lovelace',
            'message' => 'Hello from the preview!',
        ]);

        $response->assertRedirect(route('guestbook.index'));
        $response->assertSessionHas('status', 'Thanks for signing the guestbook!');
        $this->assertDatabaseHas('guestbook_entries', [
            'name' => 'Ada Lovelace',
            'message' => 'Hello from the preview!',
        ]);

        $this->get(route('guestbook.index'))
            ->assertSee('Ada Lovelace')
            ->assertSee('Hello from the preview!');
    }

    public function test_entries_are_html_escaped_when_rendered(): void
    {
        GuestbookEntry::factory()->create(['message' => '<script>alert("x")</script>']);

        $response = $this->get(route('guestbook.index'));

        $response->assertDontSee('<script>alert("x")</script>', escape: false);
        $response->assertSee('<script>alert("x")</script>');
    }

    public function test_name_and_message_are_required(): void
    {
        $response = $this->post(route('guestbook.store'), []);

        $response->assertSessionHasErrors(['name', 'message']);
        $this->assertDatabaseCount('guestbook_entries', 0);
    }

    public function test_name_may_not_exceed_100_characters(): void
    {
        $response = $this->post(route('guestbook.store'), [
            'name' => str_repeat('a', 101),
            'message' => 'Valid message',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('guestbook_entries', 0);
    }

    public function test_message_may_not_exceed_1000_characters(): void
    {
        $response = $this->post(route('guestbook.store'), [
            'name' => 'Valid Name',
            'message' => str_repeat('a', 1001),
        ]);

        $response->assertSessionHasErrors('message');
        $this->assertDatabaseCount('guestbook_entries', 0);
    }

    public function test_fields_at_the_maximum_length_are_accepted(): void
    {
        $response = $this->post(route('guestbook.store'), [
            'name' => str_repeat('a', 100),
            'message' => str_repeat('b', 1000),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('guestbook_entries', 1);
    }

    public function test_signing_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->post(route('guestbook.store'), [
                'name' => "Signer {$attempt}",
                'message' => 'Hello',
            ])->assertRedirect(route('guestbook.index'));
        }

        $this->post(route('guestbook.store'), [
            'name' => 'One Too Many',
            'message' => 'Hello',
        ])->assertTooManyRequests();

        $this->assertDatabaseCount('guestbook_entries', 10);
    }
}
