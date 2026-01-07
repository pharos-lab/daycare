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

    $this->parent = User::factory()->create();
    $this->parent->assignRole('parent');
    $this->parent->profile()->create([
        'phone' => '+33612345678',
        'city' => 'Paris',
    ]);

    $this->otherParent = User::factory()->create();
    $this->otherParent->assignRole('parent');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->parent->associatedDaycares()->attach($this->daycare->id);
});

test('admin can delete parent', function () {
    $parentId = $this->parent->id;

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.parents.destroy', $this->parent));

    $response->assertRedirect(route('admin.parents.index'));
    $response->assertSessionHas('toast');

    $this->assertDatabaseMissing('users', [
        'id' => $parentId,
    ]);
});

test('parent cannot delete themselves', function () {
    $response = $this->actingAs($this->parent)
        ->delete(route('admin.parents.destroy', $this->parent));

    $response->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $this->parent->id,
    ]);
});

test('parent cannot delete other parents', function () {
    $response = $this->actingAs($this->parent)
        ->delete(route('admin.parents.destroy', $this->otherParent));

    $response->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $this->otherParent->id,
    ]);
});

test('guest cannot delete parent', function () {
    $response = $this->delete(route('admin.parents.destroy', $this->parent));

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'id' => $this->parent->id,
    ]);
});

test('parent with no profile can be deleted', function () {
    $parentWithoutProfile = User::factory()->create();
    $parentWithoutProfile->assignRole('parent');

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.parents.destroy', $parentWithoutProfile));

    $response->assertRedirect(route('admin.parents.index'));
    $response->assertSessionHas('toast');

    $this->assertDatabaseMissing('users', [
        'id' => $parentWithoutProfile->id,
    ]);
});