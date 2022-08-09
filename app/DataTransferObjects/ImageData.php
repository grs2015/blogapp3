<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;



class ImageData extends Data
{
    public function __construct(
        public readonly int $post_id,
        public readonly ?int $image_idx,
        public readonly string $slug
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::from($request->only(['post_id', 'slug', 'image_idx']));
    }

    public static function rules(): array
    {
        return [
            'post_id' => ['required', 'exists:posts,id'],
            'image_idx' => ['nullable', 'sometimes', 'integer'],
            'slug' => ['required', 'string', 'exists:posts,slug']
        ];
    }
}
