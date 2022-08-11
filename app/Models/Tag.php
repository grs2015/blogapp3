<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Filters\QueryFilter;
use Spatie\LaravelData\WithData;
use App\Models\Builders\TagBuilder;
use App\DataTransferObjects\TagData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string|null $meta_title
 * @property string|null $slug
 * @property string|null $content
 * @method static \Database\Factories\TagFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read int|null $posts_count
 */
class Tag extends Model
{
    use HasFactory, WithData;

    public $guarded = [];

    protected $dataClass = TagData::class;

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Slug attributes creation while creating instance
     * @return void
     */
    protected static function booted()
    {
        static::creating(function(Tag $tag) {
            if (!$tag->slug) {
                $tag->slug = Str::slug($tag->title, '-');
            }
        });
    }

    public function posts():BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Custom Builder
     *
     * @param [type] $query
     * @return TagBuilder
     */
    public function newEloquentBuilder($query): TagBuilder
    {
        return new TagBuilder($query);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters): Builder
    {
        return $filters->apply($builder);
    }
}
