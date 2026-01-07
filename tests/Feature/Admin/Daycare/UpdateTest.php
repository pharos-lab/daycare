<?php

use App\Models\Daycare;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('Daycare Update - Access Control', function () {
    it('allows admin to update any daycare', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create(['name' => 'Old Name']);
        $director = createDirector();

        $response = put(route('admin.daycares.update', $daycare), [
            'director_id' => $director->id,
            'name' => 'Updated Name',
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        $response->assertRedirect(route('admin.daycares.index'));
        $response->assertSessionHas('toast');
    });

    it('denies non-admin users from updating daycares', function () {
        actingAsDirector();
        
        $daycare = Daycare::factory()->create();

        $response = put(route('admin.daycares.update', $daycare), [
            'director_id' => $daycare->director_id,
            'name' => 'Updated Name',
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        $response->assertStatus(403);
    });
});

describe('Daycare Update - Validation', function () {
    it('requires director_id', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();

        $response = put(route('admin.daycares.update', $daycare), [
            'name' => 'Test',
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        $response->assertSessionHasErrors('director_id');
    });

    it('requires name', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();

        $response = put(route('admin.daycares.update', $daycare), [
            'director_id' => $daycare->director_id,
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        $response->assertSessionHasErrors('name');
    });

    it('requires valid email format', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();

        $response = put(route('admin.daycares.update', $daycare), [
            'director_id' => $daycare->director_id,
            'name' => 'Test',
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => 'invalid-email',
            'capacity' => $daycare->capacity,
        ]);

        $response->assertSessionHasErrors('email');
    });
});

describe('Daycare Update - Daycare Modification', function () {
    it('updates daycare name', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create(['name' => 'Old Name']);

        put(route('admin.daycares.update', $daycare), [
            'director_id' => $daycare->director_id,
            'name' => 'Updated Name',
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        assertDatabaseHas('daycares', [
            'id' => $daycare->id,
            'name' => 'Updated Name',
        ]);
    });

    it('updates daycare address', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create(['address' => 'Old Address']);

        put(route('admin.daycares.update', $daycare), [
            'director_id' => $daycare->director_id,
            'name' => $daycare->name,
            'address' => 'New Address',
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        assertDatabaseHas('daycares', [
            'id' => $daycare->id,
            'address' => 'New Address',
        ]);
    });

    it('updates daycare capacity', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create(['capacity' => 20]);

        put(route('admin.daycares.update', $daycare), [
            'director_id' => $daycare->director_id,
            'name' => $daycare->name,
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => 50,
        ]);

        expect($daycare->fresh()->capacity)->toBe(50);
    });

    it('can change daycare director', function () {
        actingAsAdmin();
        
        $director1 = createDirector(['name' => 'Director 1']);
        $director2 = createDirector(['name' => 'Director 2']);
        
        $daycare = Daycare::factory()->forDirector($director1)->create();
        
        expect($daycare->director->name)->toBe('Director 1');

        put(route('admin.daycares.update', $daycare), [
            'director_id' => $director2->id,
            'name' => $daycare->name,
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        $daycare->refresh();
        
        expect($daycare->director->name)->toBe('Director 2');
    });
});

describe('Daycare Update - Response', function () {
    it('redirects to daycares index after successful update', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();

        $response = put(route('admin.daycares.update', $daycare), [
            'director_id' => $daycare->director_id,
            'name' => 'Updated',
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        $response->assertRedirect(route('admin.daycares.index'));
    });

    it('returns toast message after update', function () {
        actingAsAdmin();
        
        $daycare = Daycare::factory()->create();

        $response = put(route('admin.daycares.update', $daycare), [
            'director_id' => $daycare->director_id,
            'name' => 'Updated',
            'address' => $daycare->address,
            'city' => $daycare->city,
            'postal_code' => $daycare->postal_code,
            'phone' => $daycare->phone,
            'email' => $daycare->email,
            'capacity' => $daycare->capacity,
        ]);

        $response->assertSessionHas('toast');
    });
});