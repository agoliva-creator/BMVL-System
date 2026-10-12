<?php

namespace Database\Factories;

use App\Models\Court;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Court>
 */
class CourtFactory extends Factory
{
    protected $model = Court::class;

    public function definition(): array
    {
        $sport = fake()->randomElement([
            'Badminton',
            'Basketball',
            'Volleyball',
            'Tennis',
            'Pickleball',
        ]);

        return [
            'name' => $sport . ' Court ' . fake()->unique()->numberBetween(1, 100),
            'sport_type' => $sport,
            'hourly_rate' => fake()->randomElement([200, 250, 300, 350, 400, 500]),
            'status' => fake()->randomElement(['available', 'available', 'maintenance']),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
