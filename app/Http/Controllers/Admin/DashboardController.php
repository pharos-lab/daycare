<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): Response
    {
        $stats = $this->getStatistics();
        $recentUsers = $this->getRecentUsers();

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'recent_users' => $recentUsers,
        ]);
    }

    /**
     * Get dashboard statistics
     */
    private function getStatistics(): array
    {
        return [
            'total_users' => User::count(),
            
            // Users by role
            'total_admins' => User::role('admin')->count(),
            'total_directors' => User::role('director')->count(),
            'total_staff' => User::role('staff')->count(),
            'total_parents' => User::role('parent')->count(),
            
            // TODO: Add when models are ready
            // 'total_daycares' => Daycare::count(),
            // 'total_children' => Child::count(),
            // 'total_transmissions' => Transmission::count(),
        ];
    }

    /**
     * Get recent users (last 5)
     */
    private function getRecentUsers(): array
    {
        return User::latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'created_at', 'is_active'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'roles' => $user->getRoleNames(),
                ];
            })
            ->toArray();
    }
}