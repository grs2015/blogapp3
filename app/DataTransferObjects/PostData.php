<?php

namespace App\DataTransferObjects;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Enums\PostStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use App\Enums\FavoriteStatus;
use Illuminate\Support\Carbon;
use App\DataTransferObjects\TagData;
use App\DataTransferObjects\UserData;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\DataCollection;
use App\DataTransferObjects\CategoryData;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;


class PostData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $author_id,
        public readonly ?int $parent_id,
        public readonly ?string $title,
        public readonly ?string $meta_title,
        public readonly ?string $slug,
        public readonly ?string $summary,
        #[WithCast(EnumCast::class)]
        public readonly ?PostStatus $status = PostStatus::Draft,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?DateTime $published_at,
        public readonly ?string $content,
        public ?int $views,
        public ?string $hero_image,
        // public ?string $images,
        public readonly ?int $time_to_read,
        #[WithCast(EnumCast::class)]
        public readonly ?FavoriteStatus $favorite = FavoriteStatus::Nonfavorite,
        #[DataCollectionOf(CommentData::class)]
        public readonly null|Lazy|DataCollection $comments,
        #[DataCollectionOf(PostmetaData::class)]
        public readonly null|Lazy|DataCollection $postmetas,
        #[DataCollectionOf(GalleryData::class)]
        public readonly null|Lazy|DataCollection $galleries,
        #[DataCollectionOf(TagData::class)]
        public readonly null|Lazy|DataCollection $tags,
        #[DataCollectionOf(CategoryData::class)]
        public readonly null|Lazy|DataCollection $categories,
        public readonly null|Lazy|UserData $user,
    ) {}

    public static function fromModel(Post $post): self
    {
        return self::from([
            ...$post->toArray(),
            'comments' => Lazy::whenLoaded('comments', $post, fn() => CommentData::collection($post->comments)),
            'postmetas' => Lazy::whenLoaded('postmetas', $post, fn() => PostmetaData::collection($post->postmetas)),
            'galleries' => Lazy::whenLoaded('galleries', $post, fn() => GalleryData::collection($post->galleries)),
            'tags' => Lazy::whenLoaded('tags', $post, fn() => TagData::collection($post->tags)),
            'categories' => Lazy::whenLoaded('categories', $post, fn() => CategoryData::collection($post->categories)),
            'user' => Lazy::whenLoaded('user', $post, fn() => UserData::from($post->user))
        ]);
    }

    public static function fromRequest(Request $request): self
    {
        // NOTE - Request always contains IDs for categories
        return self::from([
            ...$request->all(),
            'tags' => TagData::collection(Tag::whereIn('id', $request->collect('tag_ids'))->get()),
            'categories' => CategoryData::collection(Category::whereIn('id', $request->collect('cat_ids'))->get()),
            'user' => UserData::from(User::findOrFail($request->author_id)),
            'slug' => Str::slug($request->title, '-')
        ])->except('comments', 'postmetas', 'galleries');
    }

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'meta_title' => ['nullable', 'sometimes', 'string'],
            'summary' => ['nullable', 'sometimes', 'string'],
            'content' => ['nullable', 'sometimes', 'string'],
            'time_to_read' => ['nullable', 'sometimes', 'integer', 'between:1, 100'],
            'hero_image' => ['nullable', 'sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'images' => ['nullable', 'sometimes', 'array'],
            'tag_ids' => ['nullable', 'sometimes', 'array'],
            'cat_ids' => ['required', 'array'],
            'status' => ['nullable', 'sometimes', new Enum(PostStatus::class)],
            'favorite' => ['nullable', 'sometimes', new Enum(FavoriteStatus::class)],
            'author_id' => ['required', 'exists:users,id'],
        ];
    }

}
