<?php

use App\Models\Daycare;
use App\Models\User;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('Daycare Index - Access Control', function () {
    it('allows admin to view daycares list', function () {
        actingAsAdmin();

        $response = get(route('admin.daycares.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('admin/daycares/Index'));
    });

    it('denies access to non-admin users', function () {
        actingAsDirector();

        $response = get(route('admin.daycares.index'));

        $response->assertStatus(403);
    });

    it('redirects unauthenticated users to login', function () {
        $response = get(route('admin.daycares.index'));

        $response->assertRedirect(route('login'));
    });
});

describe('Daycare Index - Data Display', function () {
    it('displays paginated list of daycares', function () {
        actingAsAdmin();
        
        Daycare::factory()->count(20)->create();

        $response = get(route('admin.daycares.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('admin/daycares/Index')
            ->has('daycares')
            ->has('daycares.data', 15) // Default pagination is 15
            ->has('daycares.links')
        );
    });

    it('includes director information in the response', function () {
        actingAsAdmin();
        
        $director = createDirector(['name' => 'Test Director']);
        Daycare::factory()->forDirector($director)->create(['name' => 'Test Daycare']);

        $response = get(route('admin.daycares.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('daycares.data.0', fn ($daycare) => $daycare
                ->has('director')
                ->where('director.name', 'Test Director')
                ->etc()
            )
        );
    });
});

describe('Daycare Index - Filters', function () {
    it('searches daycares by name', function () {
        actingAsAdmin();

        Daycare::factory()->create(['name' => 'Sunshine Daycare']);
        Daycare::factory()->create(['name' => 'Rainbow Daycare']);
        Daycare::factory()->create(['name' => 'Happy Kids']);

        $response = get(route('admin.daycares.index', ['search' => 'Daycare']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('daycares.data', 2) // Sunshine and Rainbow
        );
    });

    it('searches daycares by city', function () {
        actingAsAdmin();

        Daycare::factory()->create(['city' => 'Paris']);
        Daycare::factory()->create(['city' => 'Lyon']);

        $response = get(route('admin.daycares.index', ['search' => 'Paris']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('daycares.data', 1)
        );
    });

    it('filters daycares by director', function () {
        actingAsAdmin();

        $director1 = createDirector();
        $director2 = createDirector();

        Daycare::factory()->count(3)->forDirector($director1)->create();
        Daycare::factory()->count(2)->forDirector($director2)->create();

        $response = get(route('admin.daycares.index', ['director_id' => $director1->id]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('daycares.data', 3)
        );
    });
});

describe('Daycare Index - Sorting', function () {
    it('sorts daycares by name ascending', function () {
        actingAsAdmin();

        Daycare::factory()->create(['name' => 'Charlie Daycare']);
        Daycare::factory()->create(['name' => 'Alpha Daycare']);
        Daycare::factory()->create(['name' => 'Beta Daycare']);

        $response = get(route('admin.daycares.index', ['sort' => 'name', 'direction' => 'asc']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('daycares.data.0.name', 'Alpha Daycare')
        );
    });

    it('sorts daycares by created_at descending by default', function () {
        actingAsAdmin();

        $oldDaycare = Daycare::factory()->create(['created_at' => now()->subDays(5)]);
        $newDaycare = Daycare::factory()->create(['created_at' => now()]);

        $response = get(route('admin.daycares.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('daycares.data.0.id', $newDaycare->id)
        );
    });
});

describe('Daycare Index - Performance', function () {
    it('loads efficiently with many daycares', function () {
        actingAsAdmin();

        Daycare::factory()->count(100)->create();

        $startTime = microtime(true);
        
        $response = get(route('admin.daycares.index'));
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertOk();
        expect($executionTime)->toBeLessThan(500);
    });
});