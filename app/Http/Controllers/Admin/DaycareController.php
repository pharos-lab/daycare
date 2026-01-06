<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Daycares\StoreDaycareRequest;
use App\Http\Requests\Admin\Daycares\UpdateDaycareRequest;
use App\Models\Daycare;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DaycareController extends Controller
{
    /**
     * Display a listing of the daycares.
     */
    public function index(Request $request): Response
    {
        $daycares = Daycare::query()
            ->with('director')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($request->director_id, function ($query, $directorId) {
                $query->where('director_id', $directorId);
            })
            ->when($request->sort, function ($query, $sort) use ($request) {
                $direction = $request->direction ?? 'asc';
                $query->orderBy($sort, $direction);
            }, function ($query) {
                $query->latest();
            })
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/daycares/Index', [
            'daycares' => $daycares->through(fn ($daycare) => [
                'id' => $daycare->id,
                'name' => $daycare->name,
                'city' => $daycare->city,
                'address' => $daycare->address,
                'capacity' => $daycare->capacity,
                'phone' => $daycare->phone,
                'email' => $daycare->email,
                'created_at' => $daycare->created_at->format('Y-m-d H:i:s'),
                'director' => $daycare->director,
            ]),
            'filters' => $request->only(['search', 'director_id', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new daycare.
     */
    public function create(): Response
    {
        $directors = User::role('director')->get(['id', 'name']);

        return Inertia::render('admin/daycares/Create', [
            'directors' => $directors,
        ]);
    }

    /**
     * Store a newly created daycare in storage.
     */
    public function store(StoreDaycareRequest $request): RedirectResponse
    {
        Daycare::create([
            'director_id' => $request->director_id,
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'France',
            'phone' => $request->phone,
            'email' => $request->email,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'opening_hours' => $request->opening_hours,
        ]);

        return redirect()->route('admin.daycares.index')
            ->with('toast', [
                'message' => 'Daycare created successfully.',
                'type' => 'success',
            ]
        );
    }

    /**
     * Display the specified daycare.
     */
    public function show(Daycare $daycare): Response
    {
        $daycare->load('director');

        return Inertia::render('admin/daycares/Show', [
            'daycare' => [
                'id' => $daycare->id,
                'name' => $daycare->name,
                'address' => $daycare->address,
                'city' => $daycare->city,
                'postal_code' => $daycare->postal_code,
                'country' => $daycare->country,
                'phone' => $daycare->phone,
                'email' => $daycare->email,
                'capacity' => $daycare->capacity,
                'description' => $daycare->description,
                'opening_hours' => $daycare->opening_hours,
                'full_address' => $daycare->full_address,
                'created_at' => $daycare->created_at->format('Y-m-d H:i:s'),
                'director' => [
                    'id' => $daycare->director->id,
                    'name' => $daycare->director->name,
                    'email' => $daycare->director->email,
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified daycare.
     */
    public function edit(Daycare $daycare): Response
    {
        $directors = User::role('director')->get(['id', 'name']);

        return Inertia::render('admin/daycares/Edit', [
            'daycare' => [
                'id' => $daycare->id,
                'director_id' => $daycare->director_id,
                'name' => $daycare->name,
                'address' => $daycare->address,
                'city' => $daycare->city,
                'postal_code' => $daycare->postal_code,
                'country' => $daycare->country,
                'phone' => $daycare->phone,
                'email' => $daycare->email,
                'capacity' => $daycare->capacity,
                'description' => $daycare->description,
                'opening_hours' => $daycare->opening_hours,
            ],
            'directors' => $directors,
        ]);
    }

    /**
     * Update the specified daycare in storage.
     */
    public function update(UpdateDaycareRequest $request, Daycare $daycare): RedirectResponse
    {
        $daycare->update([
            'director_id' => $request->director_id,
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'France',
            'phone' => $request->phone,
            'email' => $request->email,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'opening_hours' => $request->opening_hours,
        ]);

        return redirect()->route('admin.daycares.index')
            ->with('success', 'Daycare updated successfully.');
    }

    /**
     * Remove the specified daycare from storage.
     */
    public function destroy(Daycare $daycare): RedirectResponse
    {
        $daycare->delete();

        return redirect()->route('admin.daycares.index')
            ->with('success', 'Daycare deleted successfully.');
    }
}