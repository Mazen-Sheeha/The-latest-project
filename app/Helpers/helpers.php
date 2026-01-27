<?php

use App\Models\Page;

if (!function_exists('pageUrl')) {
    function pageUrl(Page $page, string $path = ''): string
    {
        // Decide scheme
        $scheme = app()->environment('local') ? 'http' : 'https';

        $domainModel = $page->domain;

        if ($domainModel) {
            // If setup_type is dns_record, use full domain as domain.trendocp.com
            if ($domainModel->setup_type === 'wildcard') {
                // Use wildcard or single
                $domain = "*." . config('app.platform_domain', 'trendocp.com');
            } else {
                // Wildcard or custom domain
                $domain = $domainModel->domain;
            }
        } else {
            // Fallback
            $domain = config('app.platform_domain', 'trendocp.com');
        }

        // Local environment override
        if (app()->environment('local')) {
            $domain = '127.0.0.1:8000';
        }

        // Base URL
        $url = "{$scheme}://{$domain}/{$page->slug}";

        // Optional extra path
        if (!empty($path)) {
            $url .= '/' . ltrim($path, '/');
        }

        return $url;
    }
}
