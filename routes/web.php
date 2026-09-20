<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/admin-only', function (Request $request) {
        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['message' => 'Admin permission confirmed']);
        }
        return Inertia::render('Security/AdminOnly', [
            'message' => 'Admin permission confirmed',
        ]);
    })->middleware('permission:admin.access')->name('admin.only');

    Route::get('/team-only', function (Request $request) {
        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['message' => 'Team permission confirmed']);
        }
        return Inertia::render('Security/TeamOnly', [
            'message' => 'Team permission confirmed',
        ]);
    })->middleware('role:admin,team_member')->name('team.only');
});
