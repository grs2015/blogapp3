<?php

namespace App\DataTransferObjects;

use App\Models\Baseinfo;
use Illuminate\Http\File;
use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;


class BaseinfoData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly ?string $meta_title,
        public readonly ?string $content,
        public ?string $hero_image,
        public readonly ?string $address,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly ?string $website,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'meta_title' => ['nullable', 'sometimes', 'string'],
            'content' => ['nullable', 'sometimes', 'string'],
            'hero_image' => ['nullable', 'sometimes', 'image'],
            'address' => ['nullable', 'sometimes', 'string'],
            'phone' => ['nullable', 'sometimes', 'string'],
            'email' => ['nullable', 'sometimes', 'email'],
            'website' => ['nullable', 'sometimes', 'string'],
        ];
    }

    // public static function fromModel(Baseinfo $baseinfo): self
    // {
    //     return self::from([
    //         ...$baseinfo->toArray(),
    //     ]);
    // }
}
