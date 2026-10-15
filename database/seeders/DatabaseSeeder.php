<?php

namespace Database\Seeders;

use App\Models\GuestbookEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        GuestbookEntry::factory(5)->create();
    }
}
