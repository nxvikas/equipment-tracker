<?php

namespace App\Http\Middleware;

use App\Http\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActive
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status !== UserStatus::ACTIVE) {
            Auth::logout();
            return redirect()->route('auth.login')->with('error', 'Ваш аккаунт не активирован');
        }

        return $next($request);
    }
}
