<?php

use App\Models\User;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('User Destroy - Access Control', function () {
    it('allows admin to delete users', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('toast');
    });

    it('denies non-admin users from deleting users', function () {
        actingAsDirector();
        
        $user = createStaff();

        $response = delete(route('admin.users.destroy', $user));

        $response->assertStatus(403);
    });
});

describe('User Destroy - Deletion', function () {
    it('deletes user from database', function () {
        actingAsAdmin();
        
        $user = createDirector();
        $userId = $user->id;

        delete(route('admin.users.destroy', $user));

        assertDatabaseMissing('users', [
            'id' => $userId,
        ]);
    });

    it('can delete director user', function () {
        actingAsAdmin();
        
        $director = createDirector();

        $response = delete(route('admin.users.destroy', $director));

        $response->assertRedirect();
        expect(User::find($director->id))->toBeNull();
    });

    it('can delete staff user', function () {
        actingAsAdmin();
        
        $staff = createStaff();

        $response = delete(route('admin.users.destroy', $staff));

        $response->assertRedirect();
        expect(User::find($staff->id))->toBeNull();
    });

    it('can delete parent user', function () {
        actingAsAdmin();
        
        $parent = createParent();

        $response = delete(route('admin.users.destroy', $parent));

        $response->assertRedirect();
        expect(User::find($parent->id))->toBeNull();
    });
});

describe('User Destroy - Self Deletion Prevention', function () {
    it('prevents admin from deleting themselves', function () {
        $admin = actingAsAdmin();

        $response = delete(route('admin.users.destroy', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        expect(User::find($admin->id))->not->toBeNull();
    });
});

describe('User Destroy - Response', function () {
    it('redirects to users index after deletion', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
    });

    it('returns toast message after deletion', function () {
        actingAsAdmin();
        
        $user = createDirector();

        $response = delete(route('admin.users.destroy', $user));

        $response->assertSessionHas('toast');
    });
});

describe('User Destroy - 404 Handling', function () {
    it('returns 404 when trying to delete non-existent user', function () {
        actingAsAdmin();

        $response = delete(route('admin.users.destroy', 99999));

        $response->assertStatus(404);
    });
});