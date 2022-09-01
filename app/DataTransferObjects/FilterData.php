<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class FilterData extends Data
{
    public function __construct(
        public readonly ?array $cat_ids = [],
        public readonly ?array $tag_ids = []
    ) {}

    public static function fromRequest(Request $request)
    {
        return self::from([
            'tag_ids' => $request->tag_ids,
            'cat_ids' => $request->cat_ids
        ]
        );
    }
}
