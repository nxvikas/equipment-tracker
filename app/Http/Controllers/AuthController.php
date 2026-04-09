<?php

namespace App\Http\Controllers;

use App\Http\Enums\UserStatus;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'status' => UserStatus::PENDING,
            'role_id' => 2
        ]);
        return redirect()->route('auth.login')
            ->with('successReg', 'Заявка отправлена. Ожидайте подтверждения администратора.');
    }

    public function auth(AuthRequest $request)
    {
        $validated = $request->validated();
        $user = User::query()->where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Неверный email или пароль'
            ])->onlyInput('email');
        }

        if ($user->status === UserStatus::PENDING) {
            Auth::logout();
            return redirect()->route('auth.login')->with('errorAuth', 'Ваш аккаунт еще не подтвержден');
        }
        if ($user->status === UserStatus::BLOCKED) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Аккаунт заблокирован'
            ]);
        }
        if ($user->status === UserStatus::REJECTED) {
            return back()->withErrors([
                'email' => 'В доступе отказано'
            ]);
        }


        Auth::login($user, $request->boolean('remember'));
        return match ($user->role->name) {
            'admin' => redirect()->route('admin.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default => redirect()->route('auth.login')
        };

    }
}
