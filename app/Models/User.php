<?php

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Support\Str;
use App\Filters\QueryFilter;
use Spatie\LaravelData\WithData;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Builders\UserBuilder;
use App\DataTransferObjects\UserData;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $mobile
 * @property string|null $published_at
 * @property string|null $last_login
 * @property string|null $intro
 * @property string|null $profile
 * @property string|null $avatar
 * @property string $role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIntro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read int|null $posts_count
 * @method static Builder|User whereAdmin()
 * @method static Builder|User whereAuthor()
 * @method static Builder|User whereRegular()
 * @method static Builder|User currentUser(int $userId)
 * @property string|null $registered_at
 * @method static Builder|User whereRegisteredAt($value)
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereTwoFactorConfirmedAt($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static Builder|User permission($permissions)
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereStatus($value)
 * @method static UserBuilder|User createEntity(array $baseAttributes)
 * @method static UserBuilder|User destroyEntity(int $entityId)
 * @method static UserBuilder|User filter(\App\Filters\QueryFilter $filters)
 * @method static UserBuilder|User getEntityById(int $entityId)
 * @method static UserBuilder|User markAsDisabled()
 * @method static UserBuilder|User markAsEnabled()
 * @method static UserBuilder|User markAsPending()
 * @method static UserBuilder|User onlyAuthors()
 * @method static UserBuilder|User onlyEnabled()
 * @method static UserBuilder|User onlyMembers()
 * @method static UserBuilder|User onlyPending()
 * @method static UserBuilder|User updateEntity(int $entityId, array $newAttributes)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, WithData;

    const ADMIN_USER = 'admin';
    const AUTHOR_USER = 'author';
    const REGULAR_USER = 'regular';

    const ENABLED = 'enabled';
    const DISABLED = 'disabled';
    const PENDING = 'pending';

    protected $dataClass = UserData::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d',
        'created_at' => 'datetime:Y-m-d',
        'status' => UserStatus::class
    ];

    /**
     * Checks the Admin status of the user
     * @return boolean
     */
    public function isAdmin():bool
    {
        return Str::contains($this->role, User::ADMIN_USER);
    }

    /**
     * Checks the Author status of the user
     * @return boolean
     */
    public function isAuthor():bool
    {
        return Str::contains($this->role, User::AUTHOR_USER);
    }

    /**
     * Checks the Regular status of the user
     * @return boolean
     */
    public function isRegular():bool
    {
        return Str::contains($this->role, User::REGULAR_USER);
    }

    /**
     * Queries the users with Admin role
     * @param Builder $builder
     * @return void
     */
    public function scopeWhereAdmin(Builder $builder):void
    {
        $builder->where('role', User::ADMIN_USER);
    }

    /**
     * Queries the users with Author role
     * @param Builder $builder
     * @return void
     */
    public function scopeWhereAuthor(Builder $builder):void
    {
        $builder->where('role', User::AUTHOR_USER);
    }

    /**
     * Queries the users with Regular role
     * @param Builder $builder
     * @return void
     */
    public function scopeWhereRegular(Builder $builder):void
    {
        $builder->where('role', User::REGULAR_USER);
    }

    /**
     * Relationship to Post model
     * @return HasMany
     */
    public function posts():HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    protected $attributes = [
        'status' => UserStatus::Pending,
    ];

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters): Builder
    {
        return $filters->apply($builder);
    }

    public function fullName(): Attribute
    {
        return Attribute::make(get: fn() => "{$this->first_name} {$this->last_name}");
    }
}
