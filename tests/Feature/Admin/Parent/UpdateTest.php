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
        'address' => '123 Main Street',
    ]);

    $this->otherParent = User::factory()->create();
    $this->otherParent->assignRole('parent');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->parent->associatedDaycares()->attach($this->daycare->id);

    $this->updateData = [
        'name' => 'Updated Parent Name',
        'email' => $this->parent->email,
        'phone' => '+33698765432',
        'address' => '456 New Street',
        'city' => 'Lyon',
        'postal_code' => '69001',
        'country' => 'France',
        'daycare_ids' => [$this->daycare->id],
    ];
});

test('admin can update parent', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $this->updateData);

    $response->assertRedirect(route('admin.parents.index'));
    $response->assertSessionHas('toast');

    $this->assertDatabaseHas('users', [
        'id' => $this->parent->id,
        'name' => 'Updated Parent Name',
    ]);

    $this->assertDatabaseHas('profiles', [
        'user_id' => $this->parent->id,
        'phone' => '+33698765432',
        'city' => 'Lyon',
    ]);
});

test('guest cannot update parent', function () {
    $response = $this->put(route('admin.parents.update', $this->parent), $this->updateData);

    $response->assertRedirect(route('login'));
});

test('parent can be updated without changing password', function () {
    $originalPassword = $this->parent->password;
    $data = $this->updateData;
    unset($data['password']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $this->parent->refresh();
    expect($this->parent->password)->toBe($originalPassword);
});

test('parent password can be updated', function () {
    $data = array_merge($this->updateData, ['password' => 'newpassword123']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $this->parent->refresh();
    expect(password_verify('newpassword123', $this->parent->password))->toBeTrue();
});

test('parent daycares can be updated', function () {
    $daycare2 = Daycare::factory()->create();
    $data = array_merge($this->updateData, [
        'daycare_ids' => [$daycare2->id],
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $this->parent->refresh();
    expect($this->parent->associatedDaycares)->toHaveCount(1);
    expect($this->parent->associatedDaycares->first()->id)->toBe($daycare2->id);
});

test('parent can be updated without daycares', function () {
    $data = array_merge($this->updateData, ['daycare_ids' => []]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $this->parent->refresh();
    expect($this->parent->daycares)->toHaveCount(0);
});

test('name is required', function () {
    $data = $this->updateData;
    unset($data['name']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertSessionHasErrors('name');
});

test('email is required', function () {
    $data = $this->updateData;
    unset($data['email']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertSessionHasErrors('email');
});

test('email must be valid', function () {
    $data = array_merge($this->updateData, ['email' => 'invalid-email']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertSessionHasErrors('email');
});

test('email must be unique except for current parent', function () {
    $existingUser = User::factory()->create();
    $data = array_merge($this->updateData, ['email' => $existingUser->email]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertSessionHasErrors('email');
});

test('parent can keep their own email', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $this->updateData);

    $response->assertRedirect(route('admin.parents.index'));
    $response->assertSessionHasNoErrors();
});

test('profile is created if it does not exist', function () {
    $parentWithoutProfile = User::factory()->create();
    $parentWithoutProfile->assignRole('parent');

    $data = array_merge($this->updateData, [
        'email' => $parentWithoutProfile->email,
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $parentWithoutProfile), $data);

    $response->assertRedirect(route('admin.parents.index'));

    $this->assertDatabaseHas('profiles', [
        'user_id' => $parentWithoutProfile->id,
        'phone' => '+33698765432',
    ]);
});

test('profile fields are optional', function () {
    $data = [
        'name' => 'Updated Name',
        'email' => $this->parent->email,
        'daycare_ids' => [],
    ];

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertRedirect(route('admin.parents.index'));
    $response->assertSessionHasNoErrors();
});

test('daycare_ids must be array', function () {
    $data = array_merge($this->updateData, ['daycare_ids' => 'not-an-array']);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertSessionHasErrors('daycare_ids');
});

test('daycare_ids must exist in database', function () {
    $data = array_merge($this->updateData, ['daycare_ids' => [99999]]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.parents.update', $this->parent), $data);

    $response->assertSessionHasErrors('daycare_ids.0');
});