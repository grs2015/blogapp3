<?php

namespace App\ViewModels;

use App\Models\Baseinfo;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use App\DataTransferObjects\BaseinfoData;

class GetBaseinfoViewModel extends ViewModel
{
    public function __construct(
        public CacheService $cacheService
    ) {}

    public function baseinfo(): BaseinfoData
    {
        return BaseinfoData::from(
            Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function() {
            return Baseinfo::first();
            })
        );
    }
}
