<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {

        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }
        $user = Auth::user();

        if ($user->status !== \App\Http\Enums\UserStatus::ACTIVE) {
            Auth::logout();
            return redirect()->route('auth.login')->with('error', 'Ваш аккаунт не активирован');
        }
        if ($user->role->name !== $role) {
            abort(403, 'Доступ запрещен. Требуется роль: ' . $role);
        }

        return $next($request);
    }
}
