<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Seeds athlete accounts only. */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');
        $emails = ['athlete@bmvl.com'];

        for ($number = 2; $number <= 10; $number++) {
            $emails[] = sprintf('athlete%02d@bmvl.test', $number);
        }

        foreach ($emails as $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => fake()->name(),
                    'phone_number' => fake()->unique()->numerify('09#########'),
                    'role' => 'athlete',
                    'password' => $password,
                ],
            );
        }
    }
}
