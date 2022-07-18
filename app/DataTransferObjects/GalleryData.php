<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class GalleryData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $post_id,
        public readonly ?string $original,
        public readonly ?string $lowres,
        public readonly ?string $thumbs
    ) {}

    public static function rules(): array
    {
        return [
            'original' => ['nullable', 'sometimes', 'string'],
            'lowres' => ['nullable', 'sometimes', 'string'],
            'thumbs' => ['nullable', 'sometimes', 'string'],
            'post_id' => ['required', 'exists:posts,id']
        ];
    }
}
