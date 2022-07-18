<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class TagData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly ?string $meta_title,
        public readonly ?string $content
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'unique:tags'],
            'meta_title' => ['nullable', 'sometimes', 'string'],
            'content' => ['nullable', 'sometimes', 'string']
        ];
    }
}
