<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use App\Rules\ParentCategory;
use Illuminate\Validation\Rule;

class CategoryData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $parent_id,
        public readonly string $title,
        public readonly ?string $meta_title,
        public readonly ?string $content,
        public readonly ?string $slug,
        public readonly ?string $icon,
        public readonly ?string $color
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::from([
            ...$request->all(),
            'slug' => Str::slug($request->title, '-')
        ]);
    }

    public static function rules(Request $request): array
    {
        return [
            'title' => ['required', 'string', Rule::unique('categories')->ignore($request->id),],
            'meta_title' => ['nullable', 'sometimes', 'string'],
            'content' => ['nullable', 'sometimes', 'string'],
            'parent_id' => ['nullable', 'sometimes', 'integer', new ParentCategory],
            'icon' => ['nullable', 'sometimes', 'string'],
            'color' => ['nullable', 'sometimes', 'string']
        ];
    }
}
