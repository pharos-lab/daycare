<?php

use App\Models\Child;
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

    $this->staff = User::factory()->create();
    $this->staff->assignRole('staff');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->child = Child::factory()->create([
        'daycare_id' => $this->daycare->id,
    ]);

    $this->child->parents()->attach($this->parent->id);
});

test('admin can delete child', function () {
    $childId = $this->child->id;

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.children.destroy', $this->child));

    $response->assertRedirect(route('admin.children.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('children', [
        'id' => $childId,
    ]);
});

test('guest cannot delete child', function () {
    $response = $this->delete(route('admin.children.destroy', $this->child));

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('children', [
        'id' => $this->child->id,
        'deleted_at' => null,
    ]);
});

test('deleting child removes parent associations', function () {
    $childId = $this->child->id;

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.children.destroy', $this->child));

    $response->assertRedirect(route('admin.children.index'));

    $this->assertDatabaseMissing('child_parent', [
        'child_id' => $childId,
    ]);
});

test('child with no parents can be deleted', function () {
    $childWithoutParents = Child::factory()->create(['daycare_id' => $this->daycare->id]);

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.children.destroy', $childWithoutParents));

    $response->assertRedirect(route('admin.children.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('children', [
        'id' => $childWithoutParents->id,
    ]);
});

test('child with multiple parents can be deleted', function () {
    $parent2 = User::factory()->create();
    $parent2->assignRole('parent');
    $this->child->parents()->attach($parent2->id);

    $childId = $this->child->id;

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.children.destroy', $this->child));

    $response->assertRedirect(route('admin.children.index'));

    $this->assertDatabaseMissing('children', [
        'id' => $childId,
    ]);

    $this->assertDatabaseMissing('child_parent', [
        'child_id' => $childId,
    ]);
});