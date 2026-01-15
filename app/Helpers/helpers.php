<?php

use App\Models\Page;

if (!function_exists('pageUrl')) {
    function pageUrl(Page $page, string $path = ''): string
    {
        $domain = $page?->website?->domain ?? '127.0.0.1:8000';

        $scheme = app()->environment('local') ? 'http' : 'https';

        if (app()->environment('local')) {
            $domain = '127.0.0.1:8000';
        }

        $url = "{$scheme}://{$domain}/buy/{$page->slug}";

        if ($path) {
            $url .= '/' . ltrim($path, '/');
        }

        return $url;
    }
}
