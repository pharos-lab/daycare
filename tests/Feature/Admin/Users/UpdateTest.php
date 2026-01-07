<?php

use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('User Update - Access Control', function () {
    it('allows admin to update users', function () {
        actingAsAdmin();
        
        $user = createDirector(['name' => 'Old Name']);

        $response = put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'director',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('toast');
    });

    it('denies non-admin users from updating users', function () {
        actingAsDirector();
        
        $user = createStaff();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'staff',
        ]);

        $response->assertStatus(403);
    });
});

describe('User Update - Validation', function () {
    it('requires name', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'email' => $user->email,
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('name');
    });

    it('requires email', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Test',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires valid email format', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Test',
            'email' => 'invalid-email',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires unique email except for current user', function () {
        actingAsAdmin();
        
        $user1 = createDirector(['email' => 'user1@test.com']);
        $user2 = createDirector(['email' => 'user2@test.com']);

        $response = put(route('admin.users.update', $user1), [
            'name' => 'Test',
            'email' => 'user2@test.com',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('allows keeping same email when updating', function () {
        actingAsAdmin();
        
        $user = createDirector(['email' => 'test@test.com', 'name' => 'Old Name']);

        $response = put(route('admin.users.update', $user), [
            'name' => 'New Name',
            'email' => 'test@test.com',
            'role' => 'director',
        ]);

        $response->assertSessionHasNoErrors();
    });

    it('requires role', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Test',
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors('role');
    });

    it('requires valid role', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Test',
            'email' => $user->email,
            'role' => 'invalid_role',
        ]);

        $response->assertSessionHasErrors('role');
    });

    it('validates password if provided', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Test',
            'email' => $user->email,
            'role' => 'director',
            'password' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('requires password confirmation if password provided', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Test',
            'email' => $user->email,
            'role' => 'director',
            'password' => 'newpassword123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
    });
});

describe('User Update - User Modification', function () {
    it('updates user name', function () {
        actingAsAdmin();
        
        $user = createDirector(['name' => 'Old Name']);

        put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'director',
        ]);

        assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    });

    it('updates user email', function () {
        actingAsAdmin();
        
        $user = createDirector(['email' => 'old@test.com']);

        put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => 'new@test.com',
            'role' => 'director',
        ]);

        assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'new@test.com',
        ]);
    });

    it('updates user password when provided', function () {
        actingAsAdmin();
        
        $user = createDirector();
        $oldPassword = $user->password;

        put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'director',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $user->refresh();
        
        expect($user->password)->not->toBe($oldPassword);
        expect(\Hash::check('newpassword123', $user->password))->toBeTrue();
    });

    it('does not update password when not provided', function () {
        actingAsAdmin();
        
        $user = createDirector();
        $oldPassword = $user->password;

        put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'director',
        ]);

        $user->refresh();
        
        expect($user->password)->toBe($oldPassword);
    });

    it('changes user role', function () {
        actingAsAdmin();
        
        $user = createDirector();
        
        expect($user->hasRole('director'))->toBeTrue();

        put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'staff',
        ]);

        $user->refresh();
        
        expect($user->hasRole('staff'))->toBeTrue();
        expect($user->hasRole('director'))->toBeFalse();
    });

    it('can promote user to admin', function () {
        actingAsAdmin();
        
        $user = createDirector();

        put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'admin',
        ]);

        $user->refresh();
        
        expect($user->hasRole('admin'))->toBeTrue();
    });
});

describe('User Update - Response', function () {
    it('redirects to users index after successful update', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Updated',
            'email' => $user->email,
            'role' => 'director',
        ]);

        $response->assertRedirect(route('admin.users.index'));
    });

    it('returns toast message after update', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = put(route('admin.users.update', $user), [
            'name' => 'Updated',
            'email' => $user->email,
            'role' => 'director',
        ]);

        $response->assertSessionHas('toast');
    });
});