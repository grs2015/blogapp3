<?php

namespace App\DataTransferObjects;

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

class UserData extends Data
{
    public function __construct(
        public readonly ?int $int,
        public readonly string $email,
        public readonly string $first_name,
        public readonly ?string $middle_name,
        public readonly ?string $last_name,
        public readonly ?string $mobile,
        public readonly ?Carbon $registered_at,
        public readonly ?Carbon $last_login,
        public readonly ?string $intro,
        public readonly ?string $profile,
        public ?string $avatar,
        #[WithCast(EnumCast::class)]
        public readonly ?UserStatus $status = UserStatus::Pending,
        public readonly ?Collection $roles,
        // #[DataCollectionOf(PostData::class)]
        // public readonly null|Lazy|DataCollection $posts,
    ) {}

    public static function fromModel(User $user): self
    {
        return self::from([
            ...$user->toArray(),
            'posts' => Lazy::whenLoaded('posts', $user, fn() => PostData::collection($user->posts)),
            'roles' => $user->roles
        ]);
    }
}
