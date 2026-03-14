<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfCustomDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        $isCustomDomain = cache()->remember(
            "domain_{$host}",
            300,
            fn() =>
            Domain::where('domain', $host)->exists()
        );

        if ($isCustomDomain) {
            abort(404);
        }

        return $next($request);
    }
}
