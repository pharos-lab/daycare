<?php

use App\Models\Daycare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->director = User::factory()->create();
    $this->director->assignRole('director');

    $this->staff = User::factory()->create();
    $this->staff->assignRole('staff');
    $this->staff->profile()->create([
        'phone' => '+33612345678',
        'city' => 'Paris',
    ]);

    $this->otherStaff = User::factory()->create();
    $this->otherStaff->assignRole('staff');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->staff->associatedDaycares()->attach($this->daycare->id);
});

test('admin can view staff index', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/staff/Index')
        ->has('staff.data')
    );
});

test('director cannot view staff index', function () {
    $response = $this->actingAs($this->director)
        ->get(route('admin.staff.index'));

    $response->assertForbidden();
});

test('staff cannot view staff index', function () {
    $response = $this->actingAs($this->staff)
        ->get(route('admin.staff.index'));

    $response->assertForbidden();
});

test('guest cannot view staff index', function () {
    $response = $this->get(route('admin.staff.index'));

    $response->assertRedirect(route('login'));
});

test('staff index displays staff with profile data', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('staff.data', fn ($staff) => $staff
            ->where('0.id', $this->staff->id)
            ->where('0.name', $this->staff->name)
            ->where('0.profile.city', 'Paris')
            ->etc()
        )
    );
});

test('staff index can be searched by name', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.index', ['search' => $this->staff->name]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('staff.data', 1)
        ->where('staff.data.0.id', $this->staff->id)
    );
});

test('staff index can be searched by email', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.index', ['search' => $this->staff->email]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('staff.data', 1)
        ->where('staff.data.0.id', $this->staff->id)
    );
});

test('staff index can be filtered by daycare', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.index', ['daycare_id' => $this->daycare->id]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('staff.data', 1)
        ->where('staff.data.0.id', $this->staff->id)
    );
});

test('staff index filters persist in query string', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.index', [
            'search' => 'test',
            'daycare_id' => $this->daycare->id,
        ]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('filters.search', 'test')
        ->where('filters.daycare_id', strval($this->daycare->id))
    );
});

test('staff index is paginated', function () {
    User::factory()->count(20)->create()->each(fn ($user) => $user->assignRole('staff'));

    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('staff.data', 15)
        ->has('staff.links')
    );
});