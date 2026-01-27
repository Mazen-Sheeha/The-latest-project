<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;

class ResolveDomain
{
    public function handle(Request $request, Closure $next)
    {

        if (app()->environment('local')) {
            $request->attributes->set('current_domain', null);
            return $next($request);
        }

        $host = $this->normalizeDomain($request->getHost());

        $platformDomains = [
            'trendocp.com',
        ];

        // Allow platform domain
        if (in_array($host, $platformDomains)) {
            $request->attributes->set('current_domain', null);
            return $next($request);
        }

        // Try exact match (custom domain)
        $domain = Domain::where('domain', $host)
            ->where('status', 'verified')
            ->first();

        // Try wildcard / subdomain match
        if (!$domain) {
            $rootDomain = $this->extractRootDomain($host);

            $domain = Domain::where('domain', $rootDomain)
                ->where('status', 'verified')
                ->first();
        }

        if (!$domain) {
            abort(404);
        }

        $request->attributes->set('current_domain', $domain);

        return $next($request);
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower($domain);

        // Remove port if exists
        $domain = explode(':', $domain)[0];

        // Remove www
        $domain = preg_replace('#^www\.#', '', $domain);

        return $domain;
    }

    private function extractRootDomain(string $host): string
    {
        $parts = explode('.', $host);

        if (count($parts) <= 2) {
            return $host;
        }

        // page1.example.com → example.com
        return implode('.', array_slice($parts, -2));
    }
}
