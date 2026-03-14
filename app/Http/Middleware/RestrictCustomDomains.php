<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictCustomDomains
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        $domain = cache()->remember(
            "domain_{$host}",
            300,
            fn() =>
            Domain::with('pages')->where('domain', $host)->first()
        );

        // Not a custom domain — main app, let everything through
        if (!$domain) {
            return $next($request);
        }

        // It IS a custom domain — only allow access to its own page slugs
        $requestedSlug = $request->route('slug') ?? $request->route('page'); // handles both {page:slug} and {slug}

        $allowedSlugs = cache()->remember(
            "domain_slugs_{$host}",
            300,
            fn() =>
            $domain->pages->pluck('slug')->all()
        );

        if (!$requestedSlug || !in_array($requestedSlug, $allowedSlugs)) {
            abort(404);
        }

        return $next($request);
    }
}
