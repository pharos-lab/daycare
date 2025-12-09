<?php

use Illuminate\Support\Facades\Route;

Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names('admin.users');
// Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->names('admin.roles');
// Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)->names('admin.permissions');
// Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class)->only(['index', 'update'])->names('admin.settings');
// Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
