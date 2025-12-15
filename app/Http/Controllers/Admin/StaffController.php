<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Staff\StoreStaffRequest;
use App\Http\Requests\Admin\Staff\UpdateStaffRequest;
use App\Models\Daycare;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff members.
     */
    public function index(Request $request): Response
    {
        $staff = User::role('staff')
            ->with(['associatedDaycares', 'profile'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->daycare_id, function ($query, $daycareId) {
                $query->whereHas('associatedDaycares', function ($q) use ($daycareId) {
                    $q->where('daycares.id', $daycareId);
                });
            })
            ->when($request->sort, function ($query, $sort) use ($request) {
                $direction = $request->direction ?? 'asc';
                $query->orderBy($sort, $direction);
            }, function ($query) {
                $query->latest();
            })
            ->paginate(15)
            ->withQueryString();

        $daycares = Daycare::select('id', 'name')->get();

        return Inertia::render('admin/staff/Index', [
            'staff' => $staff,
            'daycares' => $daycares,
            'filters' => $request->only(['search', 'daycare_id', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create(): Response
    {
        $daycares = Daycare::select('id', 'name')->get();

        return Inertia::render('admin/staff/Create', [
            'daycares' => $daycares,
        ]);
    }

    /**
     * Store a newly created staff member in storage.
     */
    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $staff->assignRole('staff');

        $staff->profile()->create([
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'France',
            'position' => $request->position,
            'hire_date' => $request->hire_date,
        ]);

        if ($request->daycare_ids) {
            $staff->associatedDaycares()->sync($request->daycare_ids);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    /**
     * Display the specified staff member.
     */
    public function show(User $staff): Response
    {
        $staff->load(['daycares.director', 'profile']);

        return Inertia::render('admin/staff/Show', [
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'phone' => $staff->profile->phone ?? null,
                'address' => $staff->profile->address ?? null,
                'city' => $staff->profile->city ?? null,
                'postal_code' => $staff->profile->postal_code ?? null,
                'country' => $staff->profile->country ?? null,
                'position' => $staff->profile->position ?? null,
                'hire_date' => $staff->profile->hire_date?->format('Y-m-d'),
                'created_at' => $staff->created_at->format('Y-m-d H:i:s'),
                'daycares' => $staff->daycares->map(fn ($daycare) => [
                    'id' => $daycare->id,
                    'name' => $daycare->name,
                    'city' => $daycare->city,
                    'director' => [
                        'id' => $daycare->director->id,
                        'name' => $daycare->director->name,
                    ],
                ]),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(User $staff): Response
    {
        $daycares = Daycare::select('id', 'name')->get();
        $staff->load(['daycares', 'profile']);

        return Inertia::render('admin/staff/Edit', [
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'phone' => $staff->profile->phone ?? null,
                'address' => $staff->profile->address ?? null,
                'city' => $staff->profile->city ?? null,
                'postal_code' => $staff->profile->postal_code ?? null,
                'country' => $staff->profile->country ?? 'France',
                'position' => $staff->profile->position ?? null,
                'hire_date' => $staff->profile->hire_date?->format('Y-m-d'),
                'daycare_ids' => $staff->daycares->pluck('id')->toArray(),
            ],
            'daycares' => $daycares,
        ]);
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(UpdateStaffRequest $request, User $staff): RedirectResponse
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        $staff->profile()->updateOrCreate(
            ['user_id' => $staff->id],
            [
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'France',
                'position' => $request->position,
                'hire_date' => $request->hire_date,
            ]
        );

        if ($request->has('daycare_ids')) {
            $staff->daycares()->sync($request->daycare_ids ?? []);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy(User $staff): RedirectResponse
    {
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}