<?php

use App\Models\Daycare;
use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

describe('Daycare Store - Access Control', function () {
    it('allows admin to create daycares', function () {
        actingAsAdmin();
        $director = createDirector();

        $daycareData = [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'country' => 'France',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ];

        $response = post(route('admin.daycares.store'), $daycareData);

        $response->assertRedirect(route('admin.daycares.index'));
        $response->assertSessionHas('success');
    });

    it('denies non-admin users from creating daycares', function () {
        actingAsDirector();
        $director = createDirector();

        $daycareData = [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ];

        $response = post(route('admin.daycares.store'), $daycareData);

        $response->assertStatus(403);
    });
});

describe('Daycare Store - Validation', function () {
    it('requires director_id', function () {
        actingAsAdmin();

        $response = post(route('admin.daycares.store'), [
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('director_id');
    });

    it('requires valid director_id', function () {
        actingAsAdmin();

        $response = post(route('admin.daycares.store'), [
            'director_id' => 99999,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('director_id');
    });

    it('requires name', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('name');
    });

    it('requires address', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('address');
    });

    it('requires city', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('city');
    });

    it('requires postal_code', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('postal_code');
    });

    it('requires phone', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('phone');
    });

    it('requires email', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires valid email format', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'invalid-email',
            'capacity' => 30,
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('requires capacity', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
        ]);

        $response->assertSessionHasErrors('capacity');
    });

    it('requires capacity to be integer', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 'not-a-number',
        ]);

        $response->assertSessionHasErrors('capacity');
    });

    it('requires capacity to be at least 1', function () {
        actingAsAdmin();
        $director = createDirector();

        $response = post(route('admin.daycares.store'), [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 0,
        ]);

        $response->assertSessionHasErrors('capacity');
    });
});

describe('Daycare Store - Daycare Creation', function () {
    it('creates daycare with correct data', function () {
        actingAsAdmin();
        $director = createDirector();

        $daycareData = [
            'director_id' => $director->id,
            'name' => 'Sunshine Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'country' => 'France',
            'phone' => '0123456789',
            'email' => 'sunshine@daycare.com',
            'capacity' => 30,
        ];

        post(route('admin.daycares.store'), $daycareData);

        assertDatabaseHas('daycares', [
            'director_id' => $director->id,
            'name' => 'Sunshine Daycare',
            'email' => 'sunshine@daycare.com',
            'capacity' => 30,
        ]);
    });

    it('assigns daycare to specified director', function () {
        actingAsAdmin();
        $director = createDirector(['name' => 'John Director']);

        $daycareData = [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ];

        post(route('admin.daycares.store'), $daycareData);

        $daycare = Daycare::where('email', 'test@daycare.com')->first();
        
        expect($daycare->director->name)->toBe('John Director');
    });

    it('sets default country to France if not provided', function () {
        actingAsAdmin();
        $director = createDirector();

        $daycareData = [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ];

        post(route('admin.daycares.store'), $daycareData);

        $daycare = Daycare::where('email', 'test@daycare.com')->first();
        
        expect($daycare->country)->toBe('France');
    });
});

describe('Daycare Store - Response', function () {
    it('redirects to daycares index after successful creation', function () {
        actingAsAdmin();
        $director = createDirector();

        $daycareData = [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ];

        $response = post(route('admin.daycares.store'), $daycareData);

        $response->assertRedirect(route('admin.daycares.index'));
    });

    it('returns success message after creation', function () {
        actingAsAdmin();
        $director = createDirector();

        $daycareData = [
            'director_id' => $director->id,
            'name' => 'Test Daycare',
            'address' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75001',
            'phone' => '0123456789',
            'email' => 'test@daycare.com',
            'capacity' => 30,
        ];

        $response = post(route('admin.daycares.store'), $daycareData);

        $response->assertSessionHas('success');
    });
});