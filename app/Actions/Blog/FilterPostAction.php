<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Enums\Filters;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use App\DataTransferObjects\FilterData;

class FilterPostAction
{
    public static function execute(FilterData $data): Collection
    {
        return app(Pipeline::class)->send(Post::query())->through(self::filters($data))->thenReturn()->get();
    }

    public static function filters(FilterData $data): array
    {
        return collect($data->toArray())->map(fn(array $ids, string $key) => Filters::from($key)->createFilter($ids))->values()->all();
    }
}
