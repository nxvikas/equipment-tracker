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

        session(['pending_user_id' => $user->id]);
        return redirect()->route('auth.waiting')
            ->with('successReg', 'Заявка отправлена. Ожидайте подтверждения администратора.');
    }

    public function showWaitingPage()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->status === UserStatus::ACTIVE) {
                return match ($user->role->name) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'employee' => redirect()->route('employee.dashboard'),
                    default => redirect()->route('auth.login')
                };
            }
            Auth::logout();
        }
        if (!session()->has('pending_user_id')) {
            return redirect()->route('auth.register');
        }
        $user = User::find(session('pending_user_id'));
        if (!$user) {
            session()->forget('pending_user_id');
            return redirect()->route('auth.register');
        }
        if ($user->status !== UserStatus::PENDING) {
            session()->forget('pending_user_id');
            if ($user->status === UserStatus::ACTIVE) {
                Auth::login($user);
                return match ($user->role->name) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'employee' => redirect()->route('employee.dashboard'),
                    default => redirect()->route('auth.login')
                };
            }
            if ($user->status === UserStatus::REJECTED) {
                return redirect()->route('auth.register')->with('error', 'Ваша заявка отклонена.');
            }
            if ($user->status === UserStatus::BLOCKED) {
                return redirect()->route('auth.login')->with('error', 'Ваш аккаунт заблокирован.');
            }
        }
        return view('auth.waiting', ['user' => $user]);
    }

    public function checkStatus(Request $request)
    {
        if (!session()->has('pending_user_id')) {
            return response()->json(['redirect' => route('auth.register')]);
        }

        $user = User::find(session('pending_user_id'));

        if (!$user) {
            session()->forget('pending_user_id');
            return response()->json(['redirect' => route('auth.register')]);
        }

        if ($user->status === UserStatus::ACTIVE) {
            session()->forget('pending_user_id');


            Auth::login($user);


            $redirect = match ($user->role->name) {
                'admin' => route('admin.dashboard'),
                'employee' => route('employee.dashboard'),
                default => route('auth.login')
            };

            return response()->json([
                'status' => 'active',
                'redirect' => $redirect
            ]);
        }

        if ($user->status === UserStatus::REJECTED) {
            session()->forget('pending_user_id');
            return response()->json([
                'status' => 'rejected',
                'redirect' => route('auth.register')
            ]);
        }

        if ($user->status === UserStatus::BLOCKED) {
            session()->forget('pending_user_id');
            return response()->json([
                'status' => 'blocked',
                'redirect' => route('auth.login')
            ]);
        }

        return response()->json([
            'status' => $user->status->value,
            'status_text' => UserStatus::ruValues()[$user->status->value] ?? $user->status->value
        ]);
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
            session(['pending_user_id' => $user->id]);
            return redirect()->route('auth.waiting')->with('info', 'Ваша заявка еще на рассмотрении');
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
        if ($user->status !== UserStatus::ACTIVE) {
            return back()->withErrors([
                'email' => 'Неизвестный статус аккаунта. Обратитесь к администратору.'
            ])->onlyInput('email');
        }
        Auth::login($user, $request->boolean('remember'));
        session()->forget('pending_user_id');

        return redirect()->intended(
            match ($user->role->name) {
                'admin' => route('admin.dashboard'),
                'employee' => route('employee.dashboard'),
                default => route('auth.login')
            }
        );

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }

}
