<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use App\Rules\ParentCategory;

class CategoryData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $parent_id,
        public readonly string $title,
        public readonly ?string $meta_title,
        public readonly ?string $content,
        public readonly ?string $slug
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::from([
            ...$request->all(),
            'slug' => Str::slug($request->title, '-')
        ]);
    }

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'unique:categories'],
            'meta_title' => ['nullable', 'sometimes', 'string'],
            'content' => ['nullable', 'sometimes', 'string'],
            'parent_id' => ['nullable', 'sometimes', 'integer', new ParentCategory],
        ];
    }
}
