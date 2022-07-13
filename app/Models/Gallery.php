<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Gallery
 *
 * @property int $id
 * @property string|null $original
 * @property string|null $lowres
 * @property string|null $thumbs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $post_id
 * @property-read \App\Models\Post $post
 * @method static \Database\Factories\GalleryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery query()
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery whereLowres($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery whereOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery whereThumbs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gallery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Gallery extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function post():BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
