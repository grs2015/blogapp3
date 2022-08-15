<?php

namespace App\DataTransferObjects;

use DateTime;
use App\Models\User;
use App\Enums\UserStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Illuminate\Support\Carbon;
use App\DataTransferObjects\PostData;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\WithCast;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class UserData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $email,
        public readonly string $first_name,
        public readonly ?string $middle_name,
        public readonly ?string $last_name,
        public readonly ?string $full_name,
        public readonly ?string $mobile,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?DateTime $registered_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?DateTime $last_login,
        public readonly ?string $intro,
        public readonly ?string $profile,
        public ?string $avatar,
        #[WithCast(EnumCast::class)]
        public readonly ?UserStatus $status = UserStatus::Pending,
        public readonly ?Collection $roles,
        public readonly ?int $posts_count,
        // #[DataCollectionOf(PostData::class)]
        // public readonly null|Lazy|DataCollection $posts,
    ) {}

    public static function fromModel(User $user): self
    {
        return self::from([
            ...$user->toArray(),
            'full_name' => $user->full_name,
            'posts' => Lazy::whenLoaded('posts', $user, fn() => PostData::collection($user->posts)),
            'roles' => $user->roles
        ]);
    }
}
