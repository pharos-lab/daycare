<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $admin = User::factory()->withoutTwoFactor()->create([
            'name' => 'Super Admin',
            'email' => 'admin@daycare.test',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create test directors
        $director1 = User::factory()->withoutTwoFactor()->create([
            'name' => 'Jean Dupont',
            'email' => 'director1@daycare.test',
            'email_verified_at' => now(),
        ]);
        $director1->assignRole('director');

        $director2 = User::factory()->withoutTwoFactor()->create([
            'name' => 'Marie Martin',
            'email' => 'director2@daycare.test',
            'email_verified_at' => now(),
        ]);
        $director2->assignRole('director');

        // Create test staff
        $staff1 = User::factory()->withoutTwoFactor()->create([
            'name' => 'Sophie Bernard',
            'email' => 'staff1@daycare.test',
            'email_verified_at' => now(),
        ]);
        $staff1->assignRole('staff');

        $staff2 = User::factory()->withoutTwoFactor()->create([
            'name' => 'Lucas Petit',
            'email' => 'staff2@daycare.test',
            'email_verified_at' => now(),
        ]);
        $staff2->assignRole('staff');

        // Create test parents
        $parent1 = User::factory()->withoutTwoFactor()->create([
            'name' => 'Emma Dubois',
            'email' => 'parent1@daycare.test',
            'email_verified_at' => now(),
        ]);
        $parent1->assignRole('parent');

        $parent2 = User::factory()->withoutTwoFactor()->create([
            'name' => 'Thomas Robert',
            'email' => 'parent2@daycare.test',
            'email_verified_at' => now(),
        ]);
        $parent2->assignRole('parent');

        $this->command->info('Admin and test users created successfully!');
        $this->command->info('Admin: admin@daycare.test / password');
    }
}
