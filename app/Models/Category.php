<?php

namespace App\Models;


use Illuminate\Support\Str;
use App\Filters\QueryFilter;
use Spatie\LaravelData\WithData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Builders\CategoryBuilder;
use App\DataTransferObjects\CategoryData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $parent_id
 * @property string $title
 * @property string|null $meta_title
 * @property string|null $slug
 * @property string|null $content
 * @method static \Database\Factories\CategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read int|null $posts_count
 * @method static CategoryBuilder|Category createEntity(array $baseAttributes)
 * @method static CategoryBuilder|Category destroyEntity(int $entityId)
 * @method static CategoryBuilder|Category detachPosts(int $entityId)
 * @method static CategoryBuilder|Category getEntityById(int $entityId)
 * @method static CategoryBuilder|Category updateEntity(int $entityId, array $newAttributes)
 */
class Category extends Model
{
    use HasFactory, WithData;

    public $guarded = [];

    protected $dataClass = CategoryData::class;

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
        static::creating(function(Category $category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->title, '-');
            }
        });
    }

    /**
     * Relationship to Post model
     * @return BelongsToMany
     */
    public function posts():BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Custom Builder
     *
     * @param [type] $query
     * @return CategoryBuilder
     */
    public function newEloquentBuilder($query): CategoryBuilder
    {
        return new CategoryBuilder($query);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters): Builder
    {
        return $filters->apply($builder);
    }
}
