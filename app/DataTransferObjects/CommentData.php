<?php

namespace App\DataTransferObjects;

use DateTime;
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
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class CommentData extends Data
{
    public function __construct(
        public readonly ?int $id,
        #[WithCast(EnumCast::class)]
        public readonly ?CommentStatus $status = CommentStatus::Pending,
        public readonly string $title,
        public readonly ?string $content,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?DateTime $published_at,
        public readonly int $post_id,
        public readonly ?string $slug,
        public readonly ?string $author
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::from([
            ...$request->all(),
            'published_at' => now()->toDateString(),
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
