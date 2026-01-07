<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Child>
 */
class ChildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daycare_id' => \App\Models\Daycare::factory(),
            'first_name' => fake()->firstName(),
            'last_name' =>  fake()->lastName(),
            'birth_date' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'emergency_contact' => fake()->phoneNumber(),
            'enrollment_date' => fake()->date(),
        ];
    }
}
