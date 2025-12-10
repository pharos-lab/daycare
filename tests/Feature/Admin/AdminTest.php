<?php

use App\Models\User;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('Admin Gate Bypass', function () {
    it('allows admin to bypass all gates', function () {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Admin should bypass any gate/policy
        expect(Gate::forUser($admin)->allows('non-existent-permission'))->toBeTrue();
        expect(Gate::forUser($admin)->allows('any-random-ability'))->toBeTrue();
    });

    it('does not allow director to bypass gates', function () {
        $director = User::factory()->create();
        $director->assignRole('director');

        // Director should not bypass gates
        expect(Gate::forUser($director)->allows('non-existent-permission'))->toBeFalse();
    });

    it('does not allow staff to bypass gates', function () {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        // Staff should not bypass gates
        expect(Gate::forUser($staff)->allows('non-existent-permission'))->toBeFalse();
    });

    it('does not allow parent to bypass gates', function () {
        $parent = User::factory()->create();
        $parent->assignRole('parent');

        // Parent should not bypass gates
        expect(Gate::forUser($parent)->allows('non-existent-permission'))->toBeFalse();
    });
});