<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreParentRequest;
use App\Http\Requests\Admin\UpdateParentRequest;
use App\Models\Daycare;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ParentController extends Controller
{
    /**
     * Display a listing of the parents.
     */
    public function index(Request $request): Response
    {
        $parents = User::role('parent')
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

        return Inertia::render('admin/parents/Index', [
            'parents' => $parents,
            'daycares' => $daycares,
            'filters' => $request->only(['search', 'daycare_id', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new parent.
     */
    public function create(): Response
    {
        $daycares = Daycare::select('id', 'name')->get();

        return Inertia::render('admin/parents/Create', [
            'daycares' => $daycares,
        ]);
    }

    /**
     * Store a newly created parent in storage.
     */
    public function store(StoreParentRequest $request): RedirectResponse
    {
        $parent = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $parent->assignRole('parent');

        $parent->profile()->create([
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'France',
        ]);

        if ($request->daycare_ids) {
            $parent->daycares()->sync($request->daycare_ids);
        }

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent created successfully.');
    }

    /**
     * Display the specified parent.
     */
    public function show(User $parent): Response
    {
        $parent->load(['associatedDaycares.director', 'profile']);

        return Inertia::render('admin/parents/Show', [
            'parent' => $parent,
        ]);
    }

    /**
     * Show the form for editing the specified parent.
     */
    public function edit(User $parent): Response
    {
        $daycares = Daycare::select('id', 'name')->get();
        $parent->load(['associatedDaycares', 'profile']);

        return Inertia::render('admin/parents/Edit', [
            'parent' => $parent,
            'daycares' => $daycares,
        ]);
    }

    /**
     * Update the specified parent in storage.
     */
    public function update(UpdateParentRequest $request, User $parent): RedirectResponse
    {
        $this->authorize('update', $parent);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $parent->update($data);

        $parent->profile()->updateOrCreate(
            ['user_id' => $parent->id],
            [
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'France',
            ]
        );

        if ($request->has('daycare_ids')) {
            $parent->daycares()->sync($request->daycare_ids ?? []);
        }

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent updated successfully.');
    }

    /**
     * Remove the specified parent from storage.
     */
    public function destroy(User $parent): RedirectResponse
    {
        $this->authorize('delete', $parent);

        $parent->delete();

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent deleted successfully.');
    }
}