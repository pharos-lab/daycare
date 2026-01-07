<?php

use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('User Store - Access Control', function () {
    it('allows admin to create users', function () {
        actingAsAdmin();

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ];

        $response = post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('toast');
    });

    it('denies non-admin users from creating users', function () {
        actingAsDirector();

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ];

        $response = post(route('admin.users.store'), $userData);

        $response->assertStatus(403);
    });
});

describe('User Store - Validation', function () {
    it('requires name', function () {
        actingAsAdmin();

        $response = post(route('admin.users.store'), [
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('name');
    });

    it('requires email', function () {
        actingAsAdmin();

        $response = post(route('admin.users.store'), [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires valid email format', function () {
        actingAsAdmin();

        $response = post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires unique email', function () {
        actingAsAdmin();

        $existingUser = User::factory()->create(['email' => 'existing@test.com']);

        $response = post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires password', function () {
        actingAsAdmin();

        $response = post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('requires password minimum 8 characters', function () {
        actingAsAdmin();

        $response = post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('requires password confirmation', function () {
        actingAsAdmin();

        $response = post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
            'role' => 'director',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('requires role', function () {
        actingAsAdmin();

        $response = post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('role');
    });

    it('requires valid role', function () {
        actingAsAdmin();

        $response = post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid_role',
        ]);

        $response->assertSessionHasErrors('role');
    });
});

describe('User Store - User Creation', function () {
    it('creates user with correct data', function () {
        actingAsAdmin();

        $userData = [
            'name' => 'New Director',
            'email' => 'director@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ];

        post(route('admin.users.store'), $userData);

        assertDatabaseHas('users', [
            'name' => 'New Director',
            'email' => 'director@test.com',
        ]);
    });

    it('hashes password correctly', function () {
        actingAsAdmin();

        $userData = [
            'name' => 'New User',
            'email' => 'user@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'staff',
        ];

        post(route('admin.users.store'), $userData);

        $user = User::where('email', 'user@test.com')->first();
        
        expect($user->password)->not->toBe('password123');
        expect(\Hash::check('password123', $user->password))->toBeTrue();
    });

    it('assigns role to user', function () {
        actingAsAdmin();

        $userData = [
            'name' => 'New Staff',
            'email' => 'staff@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'staff',
        ];

        post(route('admin.users.store'), $userData);

        $user = User::where('email', 'staff@test.com')->first();
        
        expect($user->hasRole('staff'))->toBeTrue();
    });

    it('creates admin user', function () {
        actingAsAdmin();

        $userData = [
            'name' => 'New Admin',
            'email' => 'admin2@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ];

        post(route('admin.users.store'), $userData);

        $user = User::where('email', 'admin2@test.com')->first();
        
        expect($user->hasRole('admin'))->toBeTrue();
    });
});

describe('User Store - Response', function () {
    it('redirects to users index after successful creation', function () {
        actingAsAdmin();

        $userData = [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ];

        $response = post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
    });

    it('returns toast message after creation', function () {
        actingAsAdmin();

        $userData = [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'director',
        ];

        $response = post(route('admin.users.store'), $userData);

        $response->assertSessionHas('toast');
    });
});