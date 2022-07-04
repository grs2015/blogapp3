<?php

namespace App\Models;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $parent_id
 * @property string $title
 * @property string|null $meta_title
 * @property string|null $slug
 * @property string|null $summary
 * @property string $published
 * @property string|null $published_at
 * @property string|null $content
 * @property int|null $views
 * @property string|null $hero_image
 * @property string|null $images
 * @property int|null $time_to_read
 * @property string $favorite
 * @property int $author_id
 * @method static \Database\Factories\PostFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHeroImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTimeToRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereViews($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Postmeta[] $postmetas
 * @property-read int|null $postmetas_count
 * @property-read \App\Models\User|null $user
 * @method static Builder|Post whereDraft()
 * @method static Builder|Post wherePending()
 * @method static Builder|Post whereUnpublished()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 */
class Post extends Model
{
    use HasFactory;

    /**
     * Constants for blog state
     */
    const PUBLISHED = 'published';
    const DRAFT = 'draft';
    const PENDING = 'pending';
    const UNPUBLISHED = 'unpublished';
    /**
     * Constants for favorite state
     */
    const FAVORITE = 'favorite';
    const NONFAVORITE = 'usual';

    public $guarded = [];

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
        static::creating(function(Post $post) {
            if (!$post->slug) {
                $post->slug = Str::slug($post->title, '-');
            }
        });
    }

    /**
     * Checks the Published status of the post
     * @return boolean
     */
    public function isPublished():bool
    {
        return Str::contains($this->published, Post::PUBLISHED);
    }

    /**
     * Checks the Draft status of the post
     * @return boolean
     */
    public function isDraft():bool
    {
        return Str::contains($this->published, Post::DRAFT);
    }

    /**
     * Checks the Pending status of the post
     * @return boolean
     */
    public function isPending():bool
    {
        return Str::contains($this->published, Post::PENDING);
    }

    /**
     * Checks the Unpublished status of the post
     * @return boolean
     */
    public function isUnpublished():bool
    {
        return Str::contains($this->published, Post::UNPUBLISHED);
    }

    /**
     * Queries the posts with Published status
     * @param Builder $builder
     * @return void
     */
    public function scopeWherePublished(Builder $builder):void
    {
        $builder->where('published', Post::PUBLISHED);
    }

    /**
     * Queries the posts with Draft status
     * @param Builder $builder
     * @return void
     */
    public function scopeWhereDraft(Builder $builder):void
    {
        $builder->where('published', Post::DRAFT);
    }

    /**
     * Queries the posts with Pending status
     * @param Builder $builder
     * @return void
     */
    public function scopeWherePending(Builder $builder):void
    {
        $builder->where('published', Post::PENDING);
    }

    /**
     * Queries the posts with Unpublished status
     * @param Builder $builder
     * @return void
     */
    public function scopeWhereUnpublished(Builder $builder):void
    {
        $builder->where('published', Post::UNPUBLISHED);
    }

    /**
     * Relationship to User model
     * @return BelongsTo
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Relationship to Postmeta model
     * @return HasMany
     */
    public function postmetas():HasMany
    {
        return $this->hasMany(Postmeta::class);
    }

    /**
     * Relationship to Comment model
     * @return HasMany
     */
    public function comments():HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relationship to Tag model
     * @return BelongsToMany
     */
    public function tags():BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Relationship to Category model
     * @return BelongsToMany
     */
    public function categories():BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function rate($rating, $user = null)
    {
        if ($rating > 5 || $rating < 1) {
            throw new InvalidArgumentException('Ratings must be between 1-5');
        }

        // dump(Rating::all());

        $this
            ->ratings()
        	->updateOrCreate([
                'author_id' => $user ? $user->id : auth()->id(),
                // 'post_id' => $this->id
            ], compact('rating'));

        // $this->ratings()->create(['rating' => $rating]);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function rating()
    {
        return $this->ratings->avg('rating');
    }
}
