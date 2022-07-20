<?php

namespace App\DataTransferObjects;

use App\Models\Post;
use App\Models\Comment;
use App\Enums\CommentStatus;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Attributes\WithCast;

class CommentData extends Data
{
    public function __construct(
        public readonly ?int $id,
        #[WithCast(EnumCast::class)]
        public readonly ?CommentStatus $status = CommentStatus::Pending,
        public readonly string $title,
        public readonly ?string $content,
        public readonly ?Carbon $published_at,
        public readonly int $post_id
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::from([
            ...$request->all(),
            'status' => $request->status ?? CommentStatus::Pending
        ]);
    }

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'content' => ['nullable', 'sometimes', 'string'],
            'published_at' => ['nullable', 'sometimes', 'date'],
            'post_id' => ['required', 'exists:posts,id'],
            'status' => ['nullable', 'sometimes', new Enum(CommentStatus::class)]
        ];
    }
}
