<?php

use App\Models\Page;

if (!function_exists('pageUrl')) {
    function pageUrl(Page $page, string $path = ''): string
    {
        // Decide scheme
        // $scheme = app()->environment('local') ? 'http' : 'https';
        $scheme = 'http';

        $domainModel = $page->domain;

        if ($domainModel) {
            if ($domainModel->setup_type === 'wildcard') {
                // If you want to use wildcard, just fallback to platform domain
                $domain = config('app.platform_domain', 'trendocp.com');
            } else {
                // Use custom domain
                $domain = $domainModel->domain;
            }
        } else {
            // Fallback to main app URL
            $domain = parse_url(config('app.url', 'https://trendocp.com'), PHP_URL_HOST);
        }

        // Local override
        if (app()->environment('local')) {
            $domain = '127.0.0.1:8000';
        }

        // Build full URL
        $url = rtrim($scheme . '://' . $domain, '/') . '/buy/' . ltrim($page->slug, '/');

        // Optional extra path
        if (!empty($path)) {
            $url .= '/' . ltrim($path, '/');
        }

        return $url;
    }
}
