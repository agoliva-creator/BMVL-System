<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SystemUserSeeder::class,
            UserSeeder::class,
            AnnouncementSeeder::class,
        ]);

        // Enable these when you also want the related sample data:
        $this->call(AccessTokenSeeder::class);
        $this->call(CourtSeeder::class);
        $this->call(EquipmentSeeder::class);
    }
}
