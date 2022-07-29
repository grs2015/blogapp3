<?php

namespace App\Services;

use Illuminate\Http\Request;

class CacheService
{
    public function __construct(
        public Request $request
    ) {}

    public function cacheResponse()
    {
        $currentUrl = $this->request->url();
        $currentQueryParams = $this->request->query();

        if (!$currentQueryParams) {
            return $currentUrl;
        }

        ksort($currentQueryParams);
        $queryString = http_build_query($currentQueryParams);
        return "{$currentUrl}?{$queryString}";
    }

    public function cacheTime()
    {
        return config('appconstants.cache_time');
    }
}
