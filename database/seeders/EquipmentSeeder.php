<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipmentToCreate = max(0, 10 - Equipment::count());

        if ($equipmentToCreate > 0) {
            Equipment::factory()->count($equipmentToCreate)->create();
        }
    }
}
