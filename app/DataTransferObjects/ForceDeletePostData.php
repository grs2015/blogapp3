<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class ForceDeletePostData extends Data
{
    public function __construct(
        public readonly array $ids
    ) {}

    public static function rules(): array
    {
        return [
            'ids' => ['required', 'array']
        ];
    }
}
