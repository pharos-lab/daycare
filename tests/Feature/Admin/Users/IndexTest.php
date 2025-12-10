<?php

use App\Models\User;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('User Index - Access Control', function () {
    it('allows admin to view users list', function () {
        actingAsAdmin();

        $response = get(route('admin.users.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Admin/Users/Index'));
    });

    it('denies access to non-admin users', function () {
        actingAsDirector();

        $response = get(route('admin.users.index'));

        $response->assertStatus(403);
    });

    it('redirects unauthenticated users to login', function () {
        $response = get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    });
});

describe('User Index - Data Display', function () {
    it('displays paginated list of users', function () {
        actingAsAdmin();
        
        User::factory()->count(20)->create();

        $response = get(route('admin.users.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users/Index')
            ->has('users')
            ->has('users.data', 15) // Default pagination is 15
            ->has('users.links')
        );
    });

    it('includes user roles in the response', function () {
        actingAsAdmin();
        
        $director = createDirector(['name' => 'Test Director']);

        $response = get(route('admin.users.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('users.data', fn ($users) => $users
                ->where('0.roles', fn ($roles) => in_array('director', $roles))
            )
        );
    });
});

describe('User Index - Filters', function () {
    it('filters users by role', function () {
        actingAsAdmin();

        User::factory()->count(3)->create()->each(fn($u) => $u->assignRole('director'));
        User::factory()->count(5)->create()->each(fn($u) => $u->assignRole('staff'));

        $response = get(route('admin.users.index', ['role' => 'director']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('users.data', 3)
        );
    });

    it('searches users by name', function () {
        actingAsAdmin();

        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);
        User::factory()->create(['name' => 'Bob Johnson']);

        $response = get(route('admin.users.index', ['search' => 'John']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('users.data', 2) // John Doe and Bob Johnson
        );
    });

    it('searches users by email', function () {
        actingAsAdmin();

        User::factory()->create(['email' => 'test@example.com']);
        User::factory()->create(['email' => 'another@example.com']);

        $response = get(route('admin.users.index', ['search' => 'test@example']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('users.data', 1)
        );
    });

    it('combines multiple filters', function () {
        actingAsAdmin();

        $director = User::factory()->create(['name' => 'Active Director']);
        $director->assignRole('director');

        User::factory()->create(['name' => 'Inactive Director'])->assignRole('director');
        User::factory()->create(['name' => 'Active Staff'])->assignRole('staff');

        $response = get(route('admin.users.index', [
            'role' => 'director',
            'search' => 'Active',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('users.data', 1)
        );
    });
});

describe('User Index - Sorting', function () {
    it('sorts users by name ascending', function () {
        actingAsAdmin();

        User::factory()->create(['name' => 'Charlie']);
        User::factory()->create(['name' => 'Alice']);
        User::factory()->create(['name' => 'Bob']);

        $response = get(route('admin.users.index', ['sort' => 'name', 'direction' => 'asc']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('users.data.0.name', 'Alice')
        );
    });

    it('sorts users by created_at descending by default', function () {
        actingAsAdmin();

        $oldUser = User::factory()->create(['created_at' => now()->subDays(5)]);
        $newUser = User::factory()->create(['created_at' => now()]);

        $response = get(route('admin.users.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('users.data.0.id', $newUser->id)
        );
    });
});

describe('User Index - Performance', function () {
    it('loads efficiently with many users', function () {
        actingAsAdmin();

        User::factory()->count(100)->create();

        $startTime = microtime(true);
        
        $response = get(route('admin.users.index'));
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertOk();
        expect($executionTime)->toBeLessThan(500);
    });
});