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
        'first_name' => 'Emma',
        'last_name' => 'Smith',
    ]);

    $this->child->parents()->attach($this->parent->id, ['relationship' => 'mother']);

    $this->updateData = [
        'daycare_id' => $this->daycare->id,
        'first_name' => 'Emma Updated',
        'last_name' => 'Johnson',
        'birth_date' => '2020-06-20',
        'gender' => 'female',
        'allergies' => 'Milk',
        'medical_notes' => 'Updated notes',
        'emergency_contact' => '112',
        'enrollment_date' => '2024-02-01',
        'parent_ids' => [$this->parent->id],
    ];
});

test('admin can update child', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $this->updateData);

    $response->assertRedirect(route('admin.children.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('children', [
        'id' => $this->child->id,
        'first_name' => 'Emma Updated',
        'last_name' => 'Johnson',
    ]);
});

test('guest cannot update child', function () {
    $response = $this->put(route('admin.children.update', $this->child), $this->updateData);

    $response->assertRedirect(route('login'));
});

test('child parents can be updated', function () {
    $parent2 = User::factory()->create();
    $parent2->assignRole('parent');
    
    $data = array_merge($this->updateData, [
        'parent_ids' => [$parent2->id],
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertRedirect(route('admin.children.index'));

    $this->child->refresh();
    expect($this->child->parents)->toHaveCount(1);
    expect($this->child->parents->first()->id)->toBe($parent2->id);
});

test('child can be updated without parents', function () {
    $data = array_merge($this->updateData, ['parent_ids' => []]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertRedirect(route('admin.children.index'));

    $this->child->refresh();
    expect($this->child->parents)->toHaveCount(0);
});

test('first name is required', function () {
    $data = $this->updateData;
    unset($data['first_name']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertSessionHasErrors('first_name');
});

test('last name is required', function () {
    $data = $this->updateData;
    unset($data['last_name']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertSessionHasErrors('last_name');
});

test('birth date is required', function () {
    $data = $this->updateData;
    unset($data['birth_date']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertSessionHasErrors('birth_date');
});

test('birth date cannot be in the future', function () {
    $data = array_merge($this->updateData, ['birth_date' => now()->addDay()->format('Y-m-d')]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertSessionHasErrors('birth_date');
});

test('daycare_id is required', function () {
    $data = $this->updateData;
    unset($data['daycare_id']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertSessionHasErrors('daycare_id');
});

test('daycare_id must exist', function () {
    $data = array_merge($this->updateData, ['daycare_id' => 99999]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertSessionHasErrors('daycare_id');
});

test('gender must be valid value', function () {
    $data = array_merge($this->updateData, ['gender' => 'invalid']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.children.update', $this->child), $data);

    $response->assertSessionHasErrors('gender');
});