<?php

namespace Database\Seeders;

use App\Models\Daycare;
use App\Models\User;
use Illuminate\Database\Seeder;

class DaycareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Daycare::factory()
            ->count(10)
            ->for(User::factory()->director(), 'director')
            ->has(User::factory()->count(5)->staff(), 'staff')
            ->create();

        $this->command->info('Daycares created successfully!');
        $this->command->info('Total daycares: ' . Daycare::count());
    }
}