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

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->validData = [
        'name' => 'New Parent',
        'email' => 'newparent@example.com',
        'password' => 'password123',
        'phone' => '+33612345678',
        'address' => '123 Main Street',
        'city' => 'Paris',
        'postal_code' => '75001',
        'country' => 'France',
        'daycare_ids' => [$this->daycare->id],
    ];
});

test('admin can create parent', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $this->validData);

    $response->assertRedirect(route('admin.parents.index'));
    $response->assertSessionHas('toast');

    $this->assertDatabaseHas('users', [
        'name' => 'New Parent',
        'email' => 'newparent@example.com',
    ]);

    $newParent = User::where('email', 'newparent@example.com')->first();
    expect($newParent->hasRole('parent'))->toBeTrue();
    
    $this->assertDatabaseHas('profiles', [
        'user_id' => $newParent->id,
        'phone' => '+33612345678',
        'city' => 'Paris',
    ]);
});

test('parent cannot create parent', function () {
    $response = $this->actingAs($this->parent)
        ->post(route('admin.parents.store'), $this->validData);

    $response->assertForbidden();

    $this->assertDatabaseMissing('users', [
        'email' => 'newparent@example.com',
    ]);
});

test('guest cannot create parent', function () {
    $response = $this->post(route('admin.parents.store'), $this->validData);

    $response->assertRedirect(route('login'));

    $this->assertDatabaseMissing('users', [
        'email' => 'newparent@example.com',
    ]);
});

test('parent can be created with daycares', function () {
    $daycare2 = Daycare::factory()->create();
    $data = array_merge($this->validData, [
        'daycare_ids' => [$this->daycare->id, $daycare2->id],
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $newParent = User::where('email', 'newparent@example.com')->first();
    expect($newParent->associatedDaycares)->toHaveCount(2);
});

test('parent can be created without daycares', function () {
    $data = $this->validData;
    unset($data['daycare_ids']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $newParent = User::where('email', 'newparent@example.com')->first();
    expect($newParent->associatedDaycares)->toHaveCount(0);
});

test('name is required', function () {
    $data = $this->validData;
    unset($data['name']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertSessionHasErrors('name');
});

test('email is required', function () {
    $data = $this->validData;
    unset($data['email']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertSessionHasErrors('email');
});

test('email must be valid', function () {
    $data = array_merge($this->validData, ['email' => 'invalid-email']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertSessionHasErrors('email');
});

test('email must be unique', function () {
    $existingUser = User::factory()->create();
    $data = array_merge($this->validData, ['email' => $existingUser->email]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertSessionHasErrors('email');
});

test('password is required', function () {
    $data = $this->validData;
    unset($data['password']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertSessionHasErrors('password');
});

test('phone is optional', function () {
    $data = $this->validData;
    unset($data['phone']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $newParent = User::where('email', 'newparent@example.com')->first();
    expect($newParent->profile->phone)->toBeNull();
});

test('address is optional', function () {
    $data = $this->validData;
    unset($data['address']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $newParent = User::where('email', 'newparent@example.com')->first();
    expect($newParent->profile->address)->toBeNull();
});

test('city is optional', function () {
    $data = $this->validData;
    unset($data['city']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $newParent = User::where('email', 'newparent@example.com')->first();
    expect($newParent->profile->city)->toBeNull();
});

test('daycare_ids must be array', function () {
    $data = array_merge($this->validData, ['daycare_ids' => 'not-an-array']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertSessionHasErrors('daycare_ids');
});

test('daycare_ids must exist in database', function () {
    $data = array_merge($this->validData, ['daycare_ids' => [99999]]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $data);

    $response->assertSessionHasErrors('daycare_ids.0');
});

test('password is hashed when stored', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $this->validData);

    $response->assertRedirect(route('admin.parents.index'));

    $newParent = User::where('email', 'newparent@example.com')->first();
    expect($newParent->password)->not->toBe('password123');
    expect(password_verify('password123', $newParent->password))->toBeTrue();
});

test('profile is created with user', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.parents.store'), $this->validData);

    $response->assertRedirect(route('admin.parents.index'));

    $newParent = User::where('email', 'newparent@example.com')->first();
    expect($newParent->profile)->not->toBeNull();
    expect($newParent->profile->phone)->toBe('+33612345678');
});