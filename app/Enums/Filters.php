<?php

namespace App\Enums;

use App\Filters\Filter;
use App\Filters\CatPipelineFilter;
use App\Filters\TagPipelineFilter;

enum Filters: string
{
    case Tags = 'tag_ids';
    case Cats = 'cat_ids';

    public function createFilter(array $ids): Filter
    {
        return match($this) {
            self::Tags => new TagPipelineFilter($ids),
            self::Cats => new CatPipelineFilter($ids)
        };
    }
}
