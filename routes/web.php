<?php

use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\PageController::class, 'login'])->name('login');
    Route::get('/register', [\App\Http\Controllers\PageController::class, 'register'])->name('register');
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
