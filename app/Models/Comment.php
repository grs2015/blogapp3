<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Enums\CommentStatus;
use Spatie\LaravelData\WithData;
use App\Models\Builders\CommentBuilder;
use Illuminate\Database\Eloquent\Model;
use App\DataTransferObjects\CommentData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string|null $content
 * @property string|null $published_at
 * @property string $published
 * @property int $post_id
 * @method static \Database\Factories\CommentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Post|null $post
 * @method static Builder|Comment wherePending()
 * @method static Builder|Comment whereUnpublished()
 * @method static CommentBuilder|Comment createCommentEntity(int $postId, array $commentAttributes)
 * @method static CommentBuilder|Comment createEntity(array $baseAttributes)
 * @method static CommentBuilder|Comment destroyEntity(int $entityId)
 * @method static CommentBuilder|Comment getAllCommentEntities(int $postId)
 * @method static CommentBuilder|Comment getCommentEntityById(int $postId, int $commentId)
 * @method static CommentBuilder|Comment getEntityById(int $entityId)
 * @method static CommentBuilder|Comment updateEntity(int $entityId, array $newAttributes)
 * @property CommentStatus|null $status
 * @property string|null $author
 * @method static CommentBuilder|Comment markAsPending()
 * @method static CommentBuilder|Comment markAsPublished()
 * @method static CommentBuilder|Comment markAsUnpublished()
 * @method static CommentBuilder|Comment whereAuthor($value)
 * @method static CommentBuilder|Comment whereStatus($value)
 */
class Comment extends Model
{
    use HasFactory, WithData;

    const PUBLISHED = 'published';
    const PENDING = 'pending';
    const UNPUBLISHED = 'unpublished';

    public $guarded = [];

    protected $dataClass = CommentData::class;

    protected $casts = [
        'status' => CommentStatus::class,
        'published_at' => 'immutable_datetime:Y-m-d'
    ];

    protected $attributes = [
        'status' => CommentStatus::Pending
    ];

    /**
     * Checks the Published status of the comment
     * @return boolean
     */
    public function isPublished():bool
    {
        return Str::contains($this->published, Comment::PUBLISHED);
    }

    /**
     * Checks the Pending status of the comment
     * @return boolean
     */
    public function isPending():bool
    {
        return Str::contains($this->published, Comment::PENDING);
    }

    /**
     * Checks the Unpublished status of the comment
     * @return boolean
     */
    public function isUnpublished():bool
    {
        return Str::contains($this->published, Comment::UNPUBLISHED);
    }

    /**
     * Queries the comments with Published status
     * @param Builder $builder
     * @return void
     */
    public function scopeWherePublished(Builder $builder):void
    {
        $builder->where('published', Comment::PUBLISHED);
    }

    /**
     * Queries the comments with Pending status
     * @param Builder $builder
     * @return void
     */
    public function scopeWherePending(Builder $builder):void
    {
        $builder->where('published', Comment::PENDING);
    }

    /**
     * Queries the comments with Unpublished status
     * @param Builder $builder
     * @return void
     */
    public function scopeWhereUnpublished(Builder $builder):void
    {
        $builder->where('published', Comment::UNPUBLISHED);
    }

    /**
     * Relationship to Post model
     * @return BelongsTo
     */
    public function post():BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
    /**
     * Custom query builder
     *
     * @param [type] $query
     * @return CommentBuilder
     */
    public function newEloquentBuilder($query): CommentBuilder
    {
        return new CommentBuilder($query);
    }
}
