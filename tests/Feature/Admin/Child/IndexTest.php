<?php

use App\Models\Child;
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

    $this->staff = User::factory()->create();
    $this->staff->assignRole('staff');

    $this->daycare = Daycare::factory()->create([
        'director_id' => $this->director->id,
    ]);

    $this->child = Child::factory()->create([
        'daycare_id' => $this->daycare->id,
        'first_name' => 'Emma',
        'last_name' => 'Smith',
    ]);

    $this->child->parents()->attach($this->parent->id, ['relationship' => 'mother']);
});

test('admin can view children index', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.children.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/children/Index')
        ->has('children.data')
    );
});


test('guest cannot view children index', function () {
    $response = $this->get(route('admin.children.index'));

    $response->assertRedirect(route('login'));
});

test('children index displays children data', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.children.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('children.data', fn ($children) => $children
            ->where('0.id', $this->child->id)
            ->where('0.first_name', 'Emma')
            ->where('0.last_name', 'Smith')
            ->etc()
        )
    );
});

test('children index can be searched by first name', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.children.index', ['search' => 'Emma']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('children.data', 1)
        ->where('children.data.0.id', $this->child->id)
    );
});

test('children index can be searched by last name', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.children.index', ['search' => 'Smith']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('children.data', 1)
        ->where('children.data.0.id', $this->child->id)
    );
});

test('children index can be filtered by daycare', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.children.index', ['daycare_id' => $this->daycare->id]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('children.data', 1)
        ->where('children.data.0.id', $this->child->id)
    );
});

test('children index filters persist in query string', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.children.index', [
            'search' => 'test',
            'daycare_id' => $this->daycare->id,
        ]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('filters.search', 'test')
        ->where('filters.daycare_id', $this->daycare->id)
    );
});

test('children index is paginated', function () {
    Child::factory()->count(20)->create(['daycare_id' => $this->daycare->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.children.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('children.data', 15)
        ->has('children.links')
    );
});