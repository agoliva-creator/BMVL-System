<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $courtsToCreate = max(0, 10 - Court::count());

        if ($courtsToCreate > 0) {
            Court::factory()->count($courtsToCreate)->create();
        }
    }
}
