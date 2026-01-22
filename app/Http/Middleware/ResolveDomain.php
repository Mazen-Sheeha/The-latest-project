<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;

class ResolveDomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $this->normalizeDomain($request->getHost());

        $domain = Domain::where('domain', $host)
            ->where('status', 'verified')
            ->first();

        if (!$domain) {
            abort(404);
        }

        $request->attributes->set('current_domain', $domain);

        return $next($request);
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower($domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        return rtrim($domain, '/');
    }
}
