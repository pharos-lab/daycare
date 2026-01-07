<?php

namespace Database\Seeders;

use App\Models\Daycare;
use App\Models\User;
use App\Models\Child;
use Illuminate\Database\Seeder;

class DaycareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Daycare::factory()
            ->count(5)
            ->for(User::factory()->director(), 'director')
            ->has(User::factory()->count(fake()->numberBetween(1, 3))->staff(), 'staff')
            ->has(Child::factory()->count(10)
                ->hasAttached(
                    User::factory()->count(fake()->numberBetween(1, 2))->parent(), 
                    ['relationship' => fake()->randomElement(['mother', 'father', 'guardian'])],
                    'parents'
                )
            )->create();

        $this->command->info('Daycares created successfully!');
        $this->command->info('Total daycares: ' . Daycare::count());
        $this->command->info('Total children: ' . Child::count());
        $this->command->info('Total users: ' . User::count());
    }
}