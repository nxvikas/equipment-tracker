<?php

use Illuminate\Support\Facades\Route;


Route::middleware('web')->group(function () {
    Route::get('/login-redirect', function () {
        return redirect()->route('auth.login');
    })->name('login');
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\PageController::class, 'login'])->name('auth.login');
        Route::get('/register', [\App\Http\Controllers\PageController::class, 'register'])->name('auth.register');

        Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
        Route::post('/auth', [\App\Http\Controllers\AuthController::class, 'auth'])->name('auth');

    });
    Route::middleware('auth')->group(function () {
        Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');


        Route::get('/equipment/{id}', [\App\Http\Controllers\EquipmentController::class, 'publicShow'])->name('public.equipment');
        Route::get('/equipment/{equipment}/qrcode', [\App\Http\Controllers\EquipmentController::class, 'getQrCode'])->name('equipment.qrcode');
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

    Route::middleware(['auth', 'check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/equipment', [\App\Http\Controllers\AdminController::class, 'equipment'])->name('equipment');
        Route::get('/history', [\App\Http\Controllers\AdminController::class, 'history'])->name('history');
        Route::post('/equipment', [\App\Http\Controllers\EquipmentController::class, 'store'])->name('equipment.store');
        Route::put('/equipment/{equipment}', [\App\Http\Controllers\EquipmentController::class, 'update'])->name('equipment.update');
        Route::delete('/equipment/{equipment}', [\App\Http\Controllers\EquipmentController::class, 'destroy'])->name('equipment.destroy');

        Route::post('/equipment/category', [\App\Http\Controllers\CategoryController::class, 'store'])->name('category.store');
        Route::post('/equipment/location', [\App\Http\Controllers\LocationController::class, 'store'])->name('location.store');

        Route::get('/equipment/{equipment}', [\App\Http\Controllers\EquipmentController::class, 'show'])->name('equipment.show');


        Route::post('/equipment/{equipment}/assign', [\App\Http\Controllers\EquipmentController::class, 'assign'])->name('equipment.assign');
        Route::post('/equipment/{equipment}/return', [\App\Http\Controllers\EquipmentController::class, 'return'])->name('equipment.return');
        Route::post('/equipment/{equipment}/repair', [\App\Http\Controllers\EquipmentController::class, 'repair'])->name('equipment.repair');
        Route::post('/equipment/{equipment}/return-from-repair', [\App\Http\Controllers\EquipmentController::class, 'returnFromRepair'])->name('equipment.return-from-repair');
        Route::post('/equipment/{equipment}/write-off', [\App\Http\Controllers\EquipmentController::class, 'writeOff'])->name('equipment.write-off');
        Route::post('/equipment/return-from-user', [\App\Http\Controllers\EquipmentController::class, 'returnFromUser'])
            ->name('equipment.return-from-user');
        Route::post('/equipment/assign-to-user', [\App\Http\Controllers\EquipmentController::class, 'assignToUser'])
            ->name('equipment.assign-to-user');

        Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

        Route::get('/locations', [\App\Http\Controllers\LocationController::class, 'index'])->name('locations.index');
        Route::post('/locations', [\App\Http\Controllers\LocationController::class, 'store'])->name('locations.store');
        Route::put('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'destroy'])->name('locations.destroy');
        Route::get('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'show'])->name('locations.show');

        Route::get('/structure', [\App\Http\Controllers\AdminController::class, 'structure'])->name('structure.index');
        Route::post('/departments', [\App\Http\Controllers\DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [\App\Http\Controllers\DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [\App\Http\Controllers\DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::post('/positions', [\App\Http\Controllers\PositionController::class, 'store'])->name('positions.store');
        Route::put('/positions/{position}', [\App\Http\Controllers\PositionController::class, 'update'])->name('positions.update');
        Route::delete('/positions/{position}', [\App\Http\Controllers\PositionController::class, 'destroy'])->name('positions.destroy');

        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');


        Route::post('/users/{user}/approve', [\App\Http\Controllers\UserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [\App\Http\Controllers\UserController::class, 'reject'])->name('users.reject');
        Route::post('/users/{user}/block', [\App\Http\Controllers\UserController::class, 'block'])->name('users.block');
        Route::post('/users/{user}/activate', [\App\Http\Controllers\UserController::class, 'activate'])->name('users.activate');
        Route::put('/users/{user}/quick', [\App\Http\Controllers\UserController::class, 'updateQuick'])->name('users.update.quick');
        Route::put('/users/{user}/full', [\App\Http\Controllers\UserController::class, 'updateFull'])->name('users.update.full');
        Route::post('/users/{user}/change-password', [\App\Http\Controllers\UserController::class, 'changePassword'])->name('users.change-password');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

        Route::post('/users/{user}/make-admin', [\App\Http\Controllers\UserController::class, 'makeAdmin'])->name('users.make-admin');
        Route::post('/users/{user}/remove-admin', [\App\Http\Controllers\UserController::class, 'removeAdmin'])->name('users.remove-admin');


        Route::get('/export/dashboard', [\App\Http\Controllers\AdminController::class, 'exportDashboard'])->name('export.dashboard');
        Route::get('/export/equipment', [\App\Http\Controllers\AdminController::class, 'exportEquipment'])->name('export.equipment');
        Route::get('/export/categories', [\App\Http\Controllers\AdminController::class, 'exportCategories'])->name('export.categories');
        Route::get('/export/locations', [\App\Http\Controllers\AdminController::class, 'exportLocations'])->name('export.locations');
        Route::get('/export/users', [\App\Http\Controllers\AdminController::class, 'exportUsers'])->name('export.users');
        Route::get('/export/structure', [\App\Http\Controllers\AdminController::class, 'exportStructure'])->name('export.structure');
        Route::get('/export/history', [\App\Http\Controllers\AdminController::class, 'exportHistory'])->name('export.history');


        Route::get('/search', [\App\Http\Controllers\AdminController::class, 'globalSearch'])->name('global.search');

    });


    Route::middleware(['auth', 'check.role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\EmployeeController::class, 'dashboard'])->name('dashboard');

        Route::get('/equipment', [\App\Http\Controllers\EmployeeController::class, 'myEquipment'])->name('equipment');
        Route::get('/export/equipment', [\App\Http\Controllers\EmployeeController::class, 'exportMyEquipment'])->name('export.equipment');
        Route::post('/equipment/{equipment}/return', [\App\Http\Controllers\EmployeeController::class, 'returnEquipment'])->name('equipment.return');

        Route::get('/search', [\App\Http\Controllers\EmployeeController::class, 'globalSearch'])->name('global.search');
    });
});
