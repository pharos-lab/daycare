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

    $this->staff = User::factory()->create();
    $this->staff->assignRole('staff');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->validData = [
        'daycare_id' => $this->daycare->id,
        'first_name' => 'Emma',
        'last_name' => 'Smith',
        'birth_date' => '2020-05-15',
        'gender' => 'female',
        'emergency_contact' => '911',
        'enrollment_date' => '2024-01-15',
        'parent_ids' => [$this->parent->id],
    ];
});

test('admin can create child', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $this->validData);

    $response->assertRedirect(route('admin.children.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('children', [
        'first_name' => 'Emma',
        'last_name' => 'Smith',
        'daycare_id' => $this->daycare->id,
    ]);
});

test('guest cannot create child', function () {
    $response = $this->post(route('admin.children.store'), $this->validData);

    $response->assertRedirect(route('login'));
});

test('child can be created with parents', function () {
    $parent2 = User::factory()->create();
    $parent2->assignRole('parent');
    
    $data = array_merge($this->validData, [
        'parent_ids' => [$this->parent->id, $parent2->id],
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertRedirect(route('admin.children.index'));

    $child = \App\Models\Child::where('first_name', 'Emma')->first();
    expect($child->parents)->toHaveCount(2);
});

test('child can be created without parents', function () {
    $data = $this->validData;
    unset($data['parent_ids']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertRedirect(route('admin.children.index'));

    $child = \App\Models\Child::where('first_name', 'Emma')->first();
    expect($child->parents)->toHaveCount(0);
});

test('first name is required', function () {
    $data = $this->validData;
    unset($data['first_name']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('first_name');
});

test('last name is required', function () {
    $data = $this->validData;
    unset($data['last_name']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('last_name');
});

test('birth date is required', function () {
    $data = $this->validData;
    unset($data['birth_date']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('birth_date');
});

test('birth date must be a valid date', function () {
    $data = array_merge($this->validData, ['birth_date' => 'invalid-date']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('birth_date');
});

test('birth date cannot be in the future', function () {
    $data = array_merge($this->validData, ['birth_date' => now()->addDay()->format('Y-m-d')]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('birth_date');
});

test('daycare_id is required', function () {
    $data = $this->validData;
    unset($data['daycare_id']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('daycare_id');
});

test('daycare_id must exist', function () {
    $data = array_merge($this->validData, ['daycare_id' => 99999]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('daycare_id');
});

test('gender is optional', function () {
    $data = $this->validData;
    unset($data['gender']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertRedirect(route('admin.children.index'));
});

test('gender must be valid value', function () {
    $data = array_merge($this->validData, ['gender' => 'invalid']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('gender');
});

test('parent_ids must be array', function () {
    $data = array_merge($this->validData, ['parent_ids' => 'not-an-array']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('parent_ids');
});

test('parent_ids must exist in database', function () {
    $data = array_merge($this->validData, ['parent_ids' => [99999]]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.children.store'), $data);

    $response->assertSessionHasErrors('parent_ids.0');
});