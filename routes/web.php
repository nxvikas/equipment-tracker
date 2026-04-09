<?php

use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\PageController::class, 'login'])->name('auth.login');
    Route::get('/register', [\App\Http\Controllers\PageController::class, 'register'])->name('auth.register');

    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
    Route::post('/auth', [\App\Http\Controllers\AuthController::class, 'auth'])->name('auth');
});

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role->name;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default => abort(403)
        };
    }
    return redirect()->route('register');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\PageController::class, 'adminDashboard'])
        ->name('admin.dashboard');

    Route::get('/employee/dashboard', [\App\Http\Controllers\PageController::class, 'employeeDashboard'])
        ->name('employee.dashboard');
});
