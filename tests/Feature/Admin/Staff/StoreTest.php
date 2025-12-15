<?php

use App\Models\Daycare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->director = User::factory()->create();
    $this->director->assignRole('director');

    $this->staff = User::factory()->create();
    $this->staff->assignRole('staff');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->validData = [
        'name' => 'New Staff Member',
        'email' => 'newstaff@example.com',
        'password' => 'password123',
        'phone' => '+33612345678',
        'address' => '123 Main Street',
        'city' => 'Paris',
        'postal_code' => '75001',
        'country' => 'France',
        'position' => 'Educator',
        'hire_date' => '2024-01-15',
        'daycare_ids' => [$this->daycare->id],
    ];
});

test('admin can create staff member', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $this->validData);

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'New Staff Member',
        'email' => 'newstaff@example.com',
    ]);

    $newStaff = User::where('email', 'newstaff@example.com')->first();
    expect($newStaff->hasRole('staff'))->toBeTrue();
    
    $this->assertDatabaseHas('profiles', [
        'user_id' => $newStaff->id,
        'phone' => '+33612345678',
        'city' => 'Paris',
        'position' => 'Educator',
    ]);
});

test('director can create staff member', function () {
    $response = $this->actingAs($this->director)
        ->post(route('admin.staff.store'), $this->validData);

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => 'newstaff@example.com',
    ]);
});

test('staff cannot create staff member', function () {
    $response = $this->actingAs($this->staff)
        ->post(route('admin.staff.store'), $this->validData);

    $response->assertForbidden();

    $this->assertDatabaseMissing('users', [
        'email' => 'newstaff@example.com',
    ]);
});

test('guest cannot create staff member', function () {
    $response = $this->post(route('admin.staff.store'), $this->validData);

    $response->assertRedirect(route('login'));

    $this->assertDatabaseMissing('users', [
        'email' => 'newstaff@example.com',
    ]);
});

test('staff member can be created with daycares', function () {
    $daycare2 = Daycare::factory()->create();
    $data = array_merge($this->validData, [
        'daycare_ids' => [$this->daycare->id, $daycare2->id],
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $newStaff = User::where('email', 'newstaff@example.com')->first();
    expect($newStaff->daycares)->toHaveCount(2);
});

test('staff member can be created without daycares', function () {
    $data = $this->validData;
    unset($data['daycare_ids']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $newStaff = User::where('email', 'newstaff@example.com')->first();
    expect($newStaff->daycares)->toHaveCount(0);
});

test('name is required', function () {
    $data = $this->validData;
    unset($data['name']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertSessionHasErrors('name');
});

test('email is required', function () {
    $data = $this->validData;
    unset($data['email']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertSessionHasErrors('email');
});

test('email must be valid', function () {
    $data = array_merge($this->validData, ['email' => 'invalid-email']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertSessionHasErrors('email');
});

test('email must be unique', function () {
    $existingUser = User::factory()->create();
    $data = array_merge($this->validData, ['email' => $existingUser->email]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertSessionHasErrors('email');
});

test('password is required', function () {
    $data = $this->validData;
    unset($data['password']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertSessionHasErrors('password');
});

test('phone is optional', function () {
    $data = $this->validData;
    unset($data['phone']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $newStaff = User::where('email', 'newstaff@example.com')->first();
    expect($newStaff->profile->phone)->toBeNull();
});

test('position is optional', function () {
    $data = $this->validData;
    unset($data['position']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $newStaff = User::where('email', 'newstaff@example.com')->first();
    expect($newStaff->profile->position)->toBeNull();
});

test('hire_date is optional', function () {
    $data = $this->validData;
    unset($data['hire_date']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $newStaff = User::where('email', 'newstaff@example.com')->first();
    expect($newStaff->profile->hire_date)->toBeNull();
});

test('daycare_ids must be array', function () {
    $data = array_merge($this->validData, ['daycare_ids' => 'not-an-array']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertSessionHasErrors('daycare_ids');
});

test('daycare_ids must exist in database', function () {
    $data = array_merge($this->validData, ['daycare_ids' => [99999]]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $data);

    $response->assertSessionHasErrors('daycare_ids.0');
});

test('password is hashed when stored', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.staff.store'), $this->validData);

    $response->assertRedirect(route('admin.staff.index'));

    $newStaff = User::where('email', 'newstaff@example.com')->first();
    expect($newStaff->password)->not->toBe('password123');
    expect(password_verify('password123', $newStaff->password))->toBeTrue();
});