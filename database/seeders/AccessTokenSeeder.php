<?php

namespace Database\Seeders;

use App\Models\AccessToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccessTokenSeeder extends Seeder
{
    public function run(): void
    {
        AccessToken::firstOrCreate(
            ['origin' => 'http://bmvl-system2.test'],
            ['token' => Str::random(60)],
        );
    }
}
