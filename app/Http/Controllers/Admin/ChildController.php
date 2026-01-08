<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Children\StoreChildRequest;
use App\Http\Requests\Admin\Children\UpdateChildRequest;
use App\Models\Child;
use App\Models\Daycare;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChildController extends Controller
{
    /**
     * Display a listing of the children.
     */
    public function index(Request $request): Response
    {
        $children = Child::query()
            ->with(['daycare', 'parents'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($request->daycare_id, function ($query, $daycareId) {
                $query->where('daycare_id', $daycareId);
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

        return Inertia::render('admin/children/Index', [
            'children' => $children,
            'daycares' => $daycares,
            'filters' => $request->only(['search', 'daycare_id', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new child.
     */
    public function create(): Response
    {
        $daycares = Daycare::select('id', 'name')->get();

        $parents = User::role('parent')->select('id', 'name')->get();

        return Inertia::render('admin/children/Create', [
            'daycares' => $daycares,
            'parents' => $parents,
        ]);
    }

    /**
     * Store a newly created child in storage.
     */
    public function store(StoreChildRequest $request): RedirectResponse
    {
        $child = Child::create([
            'daycare_id' => $request->daycare_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'emergency_contact' => $request->emergency_contact,
            'enrollment_date' => $request->enrollment_date,
        ]);

        if ($request->parent_ids) {
            $child->parents()->sync($request->parent_ids);
        }

        return redirect()->route('admin.children.index')
            ->with('toast', [
                'message' => 'Child created successfully.',
                'type' => 'success',
            ]);
    }

    /**
     * Display the specified child.
     */
    public function show(Child $child): Response
    {
        $child->load(['daycare.director', 'parents']);

        return Inertia::render('admin/children/Show', [
            'child' => $child
        ]);
    }

    /**
     * Show the form for editing the specified child.
     */
    public function edit(Child $child): Response
    {
        $daycares = Daycare::select('id', 'name')->get();

        $parents = User::role('parent')->select('id', 'name')->get();

        $child->load('parents');

        return Inertia::render('admin/children/Edit', [
            'child' => $child,
            'daycares' => $daycares,
            'parents' => $parents,
        ]);
    }

    /**
     * Update the specified child in storage.
     */
    public function update(UpdateChildRequest $request, Child $child): RedirectResponse
    {
        $child->update([
            'daycare_id' => $request->daycare_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'emergency_contact' => $request->emergency_contact,
            'enrollment_date' => $request->enrollment_date,
        ]);

        if ($request->has('parent_ids')) {
            $child->parents()->sync($request->parent_ids ?? []);
        }

        return redirect()->route('admin.children.index')
            ->with('toast', [
                'message' => 'Child updated successfully.',
                'type' => 'success',
            ]);
    }

    /**
     * Remove the specified child from storage.
     */
    public function destroy(Child $child): RedirectResponse
    {
        $child->delete();

        return redirect()->route('admin.children.index')
            ->with('toast', [
                'message' => 'Child deleted successfully.',
                'type' => 'success',
            ]);
    }
}