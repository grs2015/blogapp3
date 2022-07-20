<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class PostmetaData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $post_id,
        public readonly string $key,
        public readonly string $content
    ) {}

    public static function rules(): array
    {
        return [
            'key' => ['required', 'string', 'unique:postmetas'],
            'content' => ['required', 'string'],
            'post_id' => ['required', 'exists:posts,id']
        ];
    }
}
