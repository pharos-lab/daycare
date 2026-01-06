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

    $this->parent = User::factory()->create();
    $this->parent->assignRole('parent');
    $this->parent->profile()->create([
        'phone' => '+33612345678',
        'city' => 'Paris',
        'address' => '123 Main Street',
    ]);

    $this->otherParent = User::factory()->create();
    $this->otherParent->assignRole('parent');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->parent->associatedDaycares()->attach($this->daycare->id);
});

test('admin can view parents index', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.parents.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/parents/Index')
        ->has('parents.data')
    );
});

test('parent cannot view parents index', function () {
    $response = $this->actingAs($this->parent)
        ->get(route('admin.parents.index'));

    $response->assertForbidden();
});

test('guest cannot view parents index', function () {
    $response = $this->get(route('admin.parents.index'));

    $response->assertRedirect(route('login'));
});

test('parents index displays parents with profile data', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.parents.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('parents.data', fn ($parents) => $parents
            ->where('0.id', $this->parent->id)
            ->where('0.name', $this->parent->name)
            ->where('0.profile.phone', '+33612345678')
            ->where('0.profile.city', 'Paris')
            ->etc()
        )
    );
});

test('parents index can be searched by name', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.parents.index', ['search' => $this->parent->name]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('parents.data', 1)
        ->where('parents.data.0.id', $this->parent->id)
    );
});

test('parents index can be searched by email', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.parents.index', ['search' => $this->parent->email]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('parents.data', 1)
        ->where('parents.data.0.id', $this->parent->id)
    );
});

test('parents index can be filtered by daycare', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.parents.index', ['daycare_id' => $this->daycare->id]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('parents.data', 1)
        ->where('parents.data.0.id', $this->parent->id)
    );
});

test('parents index filters persist in query string', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.parents.index', [
            'search' => 'test',
            'daycare_id' => $this->daycare->id,
        ]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('filters.search', 'test')
        ->where('filters.daycare_id', strval($this->daycare->id))
    );
});

test('parents index is paginated', function () {
    User::factory()->count(20)->create()->each(fn ($user) => $user->assignRole('parent'));

    $response = $this->actingAs($this->admin)
        ->get(route('admin.parents.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('parents.data', 15)
        ->has('parents.links')
    );
});

test('parents index returns daycares list for filter', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.parents.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('daycares')
        ->where('daycares.0.id', $this->daycare->id)
    );
});