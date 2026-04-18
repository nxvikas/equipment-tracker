<?php

use Illuminate\Support\Facades\Route;


Route::get('/login-redirect', function () {
    return redirect()->route('auth.login');
})->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\PageController::class, 'login'])->name('auth.login');
    Route::get('/register', [\App\Http\Controllers\PageController::class, 'register'])->name('auth.register');

    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
    Route::post('/auth', [\App\Http\Controllers\AuthController::class, 'auth'])->name('auth');

});

Route::get('/waiting', [\App\Http\Controllers\AuthController::class, 'showWaitingPage'])->name('auth.waiting');
Route::get('/check-status', [\App\Http\Controllers\AuthController::class, 'checkStatus'])->name('check.status');


Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role->name;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default => abort(403)
        };
    }
    return redirect()->route('auth.login');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\PageController::class, 'adminDashboard'])
        ->name('admin.dashboard')->middleware('check.role:admin');


    Route::get('/employee/dashboard', [\App\Http\Controllers\PageController::class, 'employeeDashboard'])
        ->name('employee.dashboard')->middleware('check.role:employee');

    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
});
