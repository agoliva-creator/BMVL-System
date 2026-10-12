<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        $item = fake()->randomElement([
            'Badminton Racket',
            'Basketball',
            'Volleyball',
            'Tennis Racket',
            'Shuttlecock Set',
            'Knee Pads',
            'Sports Net',
            'Training Cones',
        ]);

        return [
            'name' => $item . ' ' . fake()->unique()->numberBetween(1, 100),
            'price' => fake()->randomElement([20, 30, 50, 75, 100, 150]),
            'stock' => fake()->numberBetween(5, 40),
            'status' => fake()->randomElement(['available', 'available', 'maintenance']),
            'image' => null,
        ];
    }
}
