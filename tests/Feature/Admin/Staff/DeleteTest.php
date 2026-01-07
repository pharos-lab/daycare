<?php

use App\Models\Daycare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->director = User::factory()->create();
    $this->director->assignRole('director');

    $this->staff = User::factory()->create();
    $this->staff->assignRole('staff');
    $this->staff->profile()->create([
        'phone' => '+33612345678',
        'city' => 'Paris',
        'position' => 'Educator',
    ]);

    $this->otherStaff = User::factory()->create();
    $this->otherStaff->assignRole('staff');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->staff->associatedDaycares()->attach($this->daycare->id);
});

test('admin can delete staff member', function () {
    $staffId = $this->staff->id;

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.staff.destroy', $this->staff));

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHas('toast');

    $this->assertDatabaseMissing('users', [
        'id' => $staffId,
    ]);
});

test('guest cannot delete staff member', function () {
    $response = $this->delete(route('admin.staff.destroy', $this->staff));

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'id' => $this->staff->id,
    ]);
});