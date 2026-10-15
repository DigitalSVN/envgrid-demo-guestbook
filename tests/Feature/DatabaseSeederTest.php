<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_creates_the_sample_entries(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('guestbook_entries', count(DatabaseSeeder::SAMPLE_ENTRIES));

        foreach (DatabaseSeeder::SAMPLE_ENTRIES as $entry) {
            $this->assertDatabaseHas('guestbook_entries', $entry);
        }
    }

    public function test_the_welcome_entry_is_shown_first_after_seeding(): void
    {
        $this->seed(DatabaseSeeder::class);

        $messagesNewestFirst = array_reverse(array_column(DatabaseSeeder::SAMPLE_ENTRIES, 'message'));

        $this->get(route('guestbook.index'))
            ->assertOk()
            ->assertSeeInOrder($messagesNewestFirst);
    }

    public function test_seeding_twice_does_not_duplicate_entries(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('guestbook_entries', count(DatabaseSeeder::SAMPLE_ENTRIES));
    }
}
