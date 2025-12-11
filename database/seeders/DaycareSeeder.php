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
        // Get existing directors
        $directors = User::role('director')->get();

        if ($directors->isEmpty()) {
            $this->command->warn('No directors found. Creating directors first...');
            
            // Create some directors if none exist
            $director1 = User::factory()->create([
                'name' => 'Jean Dupont',
                'email' => 'director1@daycare.test',
            ]);
            $director1->assignRole('director');

            $director2 = User::factory()->create([
                'name' => 'Marie Martin',
                'email' => 'director2@daycare.test',
            ]);
            $director2->assignRole('director');

            $directors = collect([$director1, $director2]);
        }

        // Create daycares for each director
        foreach ($directors as $director) {
            // Each director gets 1-2 daycares
            Daycare::factory()
                ->count(rand(1, 2))
                ->forDirector($director)
                ->create();
        }

        $this->command->info('Daycares created successfully!');
        $this->command->info('Total daycares: ' . Daycare::count());
    }
}