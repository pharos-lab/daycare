<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Run the seeder before each test
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('Role and Permission Seeder', function () {
    it('creates all required roles', function () {
        expect(Role::count())->toBe(4);
        
        expect(Role::where('name', 'admin')->exists())->toBeTrue();
        expect(Role::where('name', 'director')->exists())->toBeTrue();
        expect(Role::where('name', 'staff')->exists())->toBeTrue();
        expect(Role::where('name', 'parent')->exists())->toBeTrue();
    });

    it('creates all required permissions', function () {
        $expectedPermissions = [
            'daycare.manage',
            'daycare.view',
            'staff.manage',
            'child.manage',
            'child.view',
            'parent.manage',
            'parent.view',
            'transmission.manage',
            'transmission.view',
            'announcement.manage',
            'message.manage',
            'message.view',
            'message.reply',
        ];

        expect(Permission::count())->toBe(count($expectedPermissions));

        foreach ($expectedPermissions as $permission) {
            expect(Permission::where('name', $permission)->exists())
                ->toBeTrue("Permission {$permission} should exist");
        }
    });
});

describe('Admin Role', function () {
    it('has all permissions', function () {
        $admin = Role::findByName('admin');
        $allPermissions = Permission::all();

        expect($admin->permissions->count())->toBe($allPermissions->count());
        
        foreach ($allPermissions as $permission) {
            expect($admin->hasPermissionTo($permission))->toBeTrue();
        }
    });

    it('can be assigned to a user', function () {
        $user = User::factory()->create();
        $user->assignRole('admin');

        expect($user->hasRole('admin'))->toBeTrue();
        expect($user->hasPermissionTo('daycare.manage'))->toBeTrue();
    });
});

describe('Director Role', function () {
    it('has correct permissions', function () {
        $director = Role::findByName('director');
        
        $expectedPermissions = [
            'daycare.manage',
            'staff.manage',
            'child.manage',
            'parent.manage',
            'transmission.manage',
            'announcement.manage',
            'message.manage',
        ];

        expect($director->permissions->count())->toBe(count($expectedPermissions));

        foreach ($expectedPermissions as $permission) {
            expect($director->hasPermissionTo($permission))
                ->toBeTrue("Director should have {$permission}");
        }
    });

    it('does not have view-only permissions', function () {
        $director = Role::findByName('director');
        
        expect($director->hasPermissionTo('daycare.view'))->toBeFalse();
        expect($director->hasPermissionTo('child.view'))->toBeFalse();
    });

    it('can be assigned to a user', function () {
        $user = User::factory()->create();
        $user->assignRole('director');

        expect($user->hasRole('director'))->toBeTrue();
        expect($user->hasPermissionTo('daycare.manage'))->toBeTrue();
        expect($user->hasPermissionTo('staff.manage'))->toBeTrue();
    });
});

describe('Staff Role', function () {
    it('has correct permissions', function () {
        $staff = Role::findByName('staff');
        
        $expectedPermissions = [
            'daycare.view',
            'child.view',
            'parent.view',
            'transmission.manage',
            'message.manage',
        ];

        expect($staff->permissions->count())->toBe(count($expectedPermissions));

        foreach ($expectedPermissions as $permission) {
            expect($staff->hasPermissionTo($permission))
                ->toBeTrue("Staff should have {$permission}");
        }
    });

    it('cannot manage daycares or users', function () {
        $staff = Role::findByName('staff');
        
        expect($staff->hasPermissionTo('daycare.manage'))->toBeFalse();
        expect($staff->hasPermissionTo('staff.manage'))->toBeFalse();
        expect($staff->hasPermissionTo('child.manage'))->toBeFalse();
        expect($staff->hasPermissionTo('parent.manage'))->toBeFalse();
    });

    it('can be assigned to a user', function () {
        $user = User::factory()->create();
        $user->assignRole('staff');

        expect($user->hasRole('staff'))->toBeTrue();
        expect($user->hasPermissionTo('transmission.manage'))->toBeTrue();
        expect($user->hasPermissionTo('daycare.view'))->toBeTrue();
    });
});

describe('Parent Role', function () {
    it('has correct permissions', function () {
        $parent = Role::findByName('parent');
        
        $expectedPermissions = [
            'daycare.view',
            'child.view',
            'transmission.view',
            'message.view',
            'message.reply',
        ];

        expect($parent->permissions->count())->toBe(count($expectedPermissions));

        foreach ($expectedPermissions as $permission) {
            expect($parent->hasPermissionTo($permission))
                ->toBeTrue("Parent should have {$permission}");
        }
    });

    it('has only view and reply permissions', function () {
        $parent = Role::findByName('parent');
        
        expect($parent->hasPermissionTo('daycare.manage'))->toBeFalse();
        expect($parent->hasPermissionTo('child.manage'))->toBeFalse();
        expect($parent->hasPermissionTo('transmission.manage'))->toBeFalse();
        expect($parent->hasPermissionTo('message.manage'))->toBeFalse();
    });

    it('can reply to messages but not manage them', function () {
        $parent = Role::findByName('parent');
        
        expect($parent->hasPermissionTo('message.reply'))->toBeTrue();
        expect($parent->hasPermissionTo('message.manage'))->toBeFalse();
    });

    it('can be assigned to a user', function () {
        $user = User::factory()->create();
        $user->assignRole('parent');

        expect($user->hasRole('parent'))->toBeTrue();
        expect($user->hasPermissionTo('child.view'))->toBeTrue();
        expect($user->hasPermissionTo('message.reply'))->toBeTrue();
    });
});

describe('Multiple Roles', function () {
    it('allows a user to have multiple roles', function () {
        $user = User::factory()->create();
        $user->assignRole(['staff', 'parent']);

        expect($user->hasRole('staff'))->toBeTrue();
        expect($user->hasRole('parent'))->toBeTrue();
        expect($user->hasPermissionTo('transmission.manage'))->toBeTrue();
        expect($user->hasPermissionTo('message.reply'))->toBeTrue();
    });

    it('accumulates permissions from multiple roles', function () {
        $user = User::factory()->create();
        $user->assignRole(['director', 'staff']);

        expect($user->hasPermissionTo('daycare.manage'))->toBeTrue();
        expect($user->hasPermissionTo('transmission.manage'))->toBeTrue();
    });
});