<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Seeds the administrative accounts separately from athlete accounts. */
class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $users = [
            [
                'name' => 'BMVL Admin',
                'email' => 'admin@bmvl.com',
                'role' => 'admin',
                'phone_number' => '09123456789',
            ],
            [
                'name' => 'Front Desk Staff',
                'email' => 'staff@bmvl.com',
                'role' => 'staff',
                'phone_number' => '09987654321',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [...$user, 'password' => $password],
            );
        }
    }
}
