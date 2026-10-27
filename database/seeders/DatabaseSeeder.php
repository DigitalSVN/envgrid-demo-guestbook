<?php

namespace Database\Seeders;

use App\Models\GuestbookEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Fixed sample entries, listed oldest first so the welcome note ends up on top.
     *
     * Deliberately not factory/Faker based: previews run `composer install --no-dev`,
     * so Faker (a dev dependency) isn't installed when EnvGrid runs `db:seed`.
     *
     * @var list<array{name: string, message: string}>
     */
    public const SAMPLE_ENTRIES = [
        [
            'name' => 'Grace',
            'message' => 'Opened a PR, clicked the link, and here I am. Neat.',
        ],
        [
            'name' => 'Linus',
            'message' => 'Each pull request gets its own database, so whatever you write here stays in this preview.',
        ],
        [
            'name' => 'The EnvGrid team',
            'message' => 'Welcome! This is a live preview of a real Laravel app. Sign below to try it.',
        ],
    ];

    /**
     * Seed the application's database. Safe to run repeatedly.
     */
    public function run(): void
    {
        foreach (self::SAMPLE_ENTRIES as $entry) {
            GuestbookEntry::firstOrCreate($entry);
        }
    }
}
