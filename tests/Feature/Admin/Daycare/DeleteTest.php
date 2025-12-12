<?php

use App\Models\Daycare;
use function Pest\Laravel\delete;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('Daycare Destroy - Access Control', function () {
    it('allows admin to delete daycares', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();

        $response = delete(route('admin.daycares.destroy', $daycare));

        $response->assertRedirect(route('admin.daycares.index'));
        $response->assertSessionHas('success');
    });

    it('denies non-admin users from deleting daycares', function () {
        actingAsDirector();
        
        $daycare = Daycare::factory()->create();

        $response = delete(route('admin.daycares.destroy', $daycare));

        $response->assertStatus(403);
    });
});

describe('Daycare Destroy - Deletion', function () {
    it('soft deletes daycare from database', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();
        $daycareId = $daycare->id;

        delete(route('admin.daycares.destroy', $daycare));

        // Should not be found normally
        expect(Daycare::find($daycareId))->toBeNull();
        
        // But should exist with trashed
        expect(Daycare::withTrashed()->find($daycareId))->not->toBeNull();
    });

    it('can delete daycare regardless of director', function () {
        actingAsAdmin();
        
        $director = createDirector();
        $daycare = Daycare::factory()->forDirector($director)->create();

        $response = delete(route('admin.daycares.destroy', $daycare));

        $response->assertRedirect();
        expect(Daycare::find($daycare->id))->toBeNull();
    });
});

describe('Daycare Destroy - Response', function () {
    it('redirects to daycares index after deletion', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();

        $response = delete(route('admin.daycares.destroy', $daycare));

        $response->assertRedirect(route('admin.daycares.index'));
    });

    it('returns success message after deletion', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();

        $response = delete(route('admin.daycares.destroy', $daycare));

        $response->assertSessionHas('success');
    });
});

describe('Daycare Destroy - 404 Handling', function () {
    it('returns 404 when trying to delete non-existent daycare', function () {
        actingAsAdmin();

        $response = delete(route('admin.daycares.destroy', 99999));

        $response->assertStatus(404);
    });
});