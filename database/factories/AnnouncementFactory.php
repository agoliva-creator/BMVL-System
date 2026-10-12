<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'title' => 'Welcome to BMVL Sports Center',
            'content' => 'Use your account to view facilities, request court bookings, and check the latest updates from our team.',
            'status' => 'active',
            'target_audience' => 'all',
            'pinned' => false,
        ];
    }
}
