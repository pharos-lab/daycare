<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Daycare>
 */
class DaycareFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'director_id' => User::factory()->create()->assignRole('director')->id,
            'name' => fake()->company() . ' Daycare',
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => 'France',
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'capacity' => fake()->numberBetween(10, 50),
            'opening_hours' => [
                'monday' => ['open' => '08:00', 'close' => '18:00'],
                'tuesday' => ['open' => '08:00', 'close' => '18:00'],
                'wednesday' => ['open' => '08:00', 'close' => '18:00'],
                'thursday' => ['open' => '08:00', 'close' => '18:00'],
                'friday' => ['open' => '08:00', 'close' => '18:00'],
                'saturday' => ['open' => null, 'close' => null],
                'sunday' => ['open' => null, 'close' => null],
            ],
            'description' => fake()->optional()->paragraph(),
        ];
    }

    /**
     * Set a specific director for the daycare.
     */
    public function forDirector(User $director): static
    {
        return $this->state(fn (array $attributes) => [
            'director_id' => $director->id,
        ]);
    }
}