<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreIntendedUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')
            && !$request->ajax()
            && !$request->is('login', 'register', 'logout', 'waiting')
            && $this->isInternalUrl($request->fullUrl())) {

            session(['url.intended' => $request->fullUrl()]);
        }

        return $next($request);
    }
    protected function isInternalUrl(string $url): bool
    {
        $appUrl = parse_url(config('app.url'), PHP_URL_HOST);
        $requestUrl = parse_url($url, PHP_URL_HOST);

        return $requestUrl === false || $requestUrl === $appUrl;
    }
}
