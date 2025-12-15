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
    $response->assertSessionHas('success');

    $this->assertSoftDeleted('users', [
        'id' => $staffId,
    ]);
});

test('director can delete staff in their daycares', function () {
    $staffId = $this->staff->id;

    $response = $this->actingAs($this->director)
        ->delete(route('admin.staff.destroy', $this->staff));

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHas('success');

    $this->assertSoftDeleted('users', [
        'id' => $staffId,
    ]);
});

test('director cannot delete staff not in their daycares', function () {
    $response = $this->actingAs($this->director)
        ->delete(route('admin.staff.destroy', $this->otherStaff));

    $response->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $this->otherStaff->id,
        'deleted_at' => null,
    ]);
});

test('staff cannot delete themselves', function () {
    $response = $this->actingAs($this->staff)
        ->delete(route('admin.staff.destroy', $this->staff));

    $response->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $this->staff->id,
        'deleted_at' => null,
    ]);
});

test('staff cannot delete other staff', function () {
    $response = $this->actingAs($this->staff)
        ->delete(route('admin.staff.destroy', $this->otherStaff));

    $response->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $this->otherStaff->id,
        'deleted_at' => null,
    ]);
});

test('guest cannot delete staff member', function () {
    $response = $this->delete(route('admin.staff.destroy', $this->staff));

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'id' => $this->staff->id,
        'deleted_at' => null,
    ]);
});

test('deleting staff removes daycare associations', function () {
    $staffId = $this->staff->id;

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.staff.destroy', $this->staff));

    $response->assertRedirect(route('admin.staff.index'));

    $this->assertDatabaseMissing('daycare_user', [
        'user_id' => $staffId,
    ]);
});

test('deleting staff cascades to profile', function () {
    $staffId = $this->staff->id;

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.staff.destroy', $this->staff));

    $response->assertRedirect(route('admin.staff.index'));

    // Profile should be deleted because of cascade on delete
    $this->assertDatabaseMissing('profiles', [
        'user_id' => $staffId,
    ]);
});

test('staff with no profile can be deleted', function () {
    $staffWithoutProfile = User::factory()->create();
    $staffWithoutProfile->assignRole('staff');

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.staff.destroy', $staffWithoutProfile));

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHas('success');

    $this->assertSoftDeleted('users', [
        'id' => $staffWithoutProfile->id,
    ]);
});

test('staff with multiple daycares can be deleted', function () {
    $daycare2 = Daycare::factory()->create();
    $this->staff->daycares()->attach($daycare2->id);

    $staffId = $this->staff->id;

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.staff.destroy', $this->staff));

    $response->assertRedirect(route('admin.staff.index'));

    $this->assertSoftDeleted('users', [
        'id' => $staffId,
    ]);

    $this->assertDatabaseMissing('daycare_user', [
        'user_id' => $staffId,
    ]);
});