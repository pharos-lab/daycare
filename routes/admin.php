<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureHasRole;

Route::middleware(['auth', EnsureHasRole::class . ':admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
         Route::resource('users', UserController::class);
        //  Route::resource('daycares', DaycareController::class);
        //  Route::resource('messages', MessageController::class);
     });
// Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->names('admin.roles');
// Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)->names('admin.permissions');
// Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class)->only(['index', 'update'])->names('admin.settings');
// Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
