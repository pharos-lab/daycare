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
    $this->staff->profile()->create([
        'phone' => '+33612345678',
        'city' => 'Paris',
        'position' => 'Educator',
        'hire_date' => '2024-01-15',
    ]);

    $this->otherStaff = User::factory()->create();
    $this->otherStaff->assignRole('staff');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->staff->daycares()->attach($this->daycare->id);

    $this->updateData = [
        'name' => 'Updated Staff Name',
        'email' => $this->staff->email,
        'phone' => '+33698765432',
        'address' => '456 New Street',
        'city' => 'Lyon',
        'postal_code' => '69001',
        'country' => 'France',
        'position' => 'Senior Educator',
        'hire_date' => '2024-02-01',
        'daycare_ids' => [$this->daycare->id],
    ];
});

test('admin can update staff member', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $this->updateData);

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $this->staff->id,
        'name' => 'Updated Staff Name',
    ]);

    $this->assertDatabaseHas('profiles', [
        'user_id' => $this->staff->id,
        'phone' => '+33698765432',
        'city' => 'Lyon',
        'position' => 'Senior Educator',
    ]);
});

test('director can update staff in their daycares', function () {
    $response = $this->actingAs($this->director)
        ->put(route('admin.staff.update', $this->staff), $this->updateData);

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $this->staff->id,
        'name' => 'Updated Staff Name',
    ]);
});

test('director cannot update staff not in their daycares', function () {
    $response = $this->actingAs($this->director)
        ->put(route('admin.staff.update', $this->otherStaff), $this->updateData);

    $response->assertForbidden();
});

test('staff can update their own profile', function () {
    $response = $this->actingAs($this->staff)
        ->put(route('admin.staff.update', $this->staff), $this->updateData);

    $response->assertRedirect(route('admin.staff.index'));

    $this->assertDatabaseHas('users', [
        'id' => $this->staff->id,
        'name' => 'Updated Staff Name',
    ]);
});

test('staff cannot update other staff profiles', function () {
    $response = $this->actingAs($this->staff)
        ->put(route('admin.staff.update', $this->otherStaff), $this->updateData);

    $response->assertForbidden();
});

test('guest cannot update staff member', function () {
    $response = $this->put(route('admin.staff.update', $this->staff), $this->updateData);

    $response->assertRedirect(route('login'));
});

test('staff member can be updated without changing password', function () {
    $originalPassword = $this->staff->password;
    $data = $this->updateData;
    unset($data['password']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $this->staff->refresh();
    expect($this->staff->password)->toBe($originalPassword);
});

test('staff member password can be updated', function () {
    $data = array_merge($this->updateData, ['password' => 'newpassword123']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $this->staff->refresh();
    expect(password_verify('newpassword123', $this->staff->password))->toBeTrue();
});

test('staff member daycares can be updated', function () {
    $daycare2 = Daycare::factory()->create();
    $data = array_merge($this->updateData, [
        'daycare_ids' => [$daycare2->id],
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $this->staff->refresh();
    expect($this->staff->daycares)->toHaveCount(1);
    expect($this->staff->daycares->first()->id)->toBe($daycare2->id);
});

test('staff member can be updated without daycares', function () {
    $data = array_merge($this->updateData, ['daycare_ids' => []]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $this->staff->refresh();
    expect($this->staff->daycares)->toHaveCount(0);
});

test('name is required', function () {
    $data = $this->updateData;
    unset($data['name']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertSessionHasErrors('name');
});

test('email is required', function () {
    $data = $this->updateData;
    unset($data['email']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertSessionHasErrors('email');
});

test('email must be valid', function () {
    $data = array_merge($this->updateData, ['email' => 'invalid-email']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertSessionHasErrors('email');
});

test('email must be unique except for current staff', function () {
    $existingUser = User::factory()->create();
    $data = array_merge($this->updateData, ['email' => $existingUser->email]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertSessionHasErrors('email');
});

test('staff can keep their own email', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $this->updateData);

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHasNoErrors();
});

test('profile is created if it does not exist', function () {
    $staffWithoutProfile = User::factory()->create();
    $staffWithoutProfile->assignRole('staff');

    $data = array_merge($this->updateData, [
        'email' => $staffWithoutProfile->email,
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $staffWithoutProfile), $data);

    $response->assertRedirect(route('admin.staff.index'));

    $this->assertDatabaseHas('profiles', [
        'user_id' => $staffWithoutProfile->id,
        'phone' => '+33698765432',
    ]);
});

test('profile fields are optional', function () {
    $data = [
        'name' => 'Updated Name',
        'email' => $this->staff->email,
        'daycare_ids' => [],
    ];

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertRedirect(route('admin.staff.index'));
    $response->assertSessionHasNoErrors();
});

test('daycare_ids must be array', function () {
    $data = array_merge($this->updateData, ['daycare_ids' => 'not-an-array']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertSessionHasErrors('daycare_ids');
});

test('daycare_ids must exist in database', function () {
    $data = array_merge($this->updateData, ['daycare_ids' => [99999]]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.staff.update', $this->staff), $data);

    $response->assertSessionHasErrors('daycare_ids.0');
});