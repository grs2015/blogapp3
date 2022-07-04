<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Postmeta
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $key
 * @property string|null $content
 * @property int $post_id
 * @method static \Database\Factories\PostmetaFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta query()
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Postmeta whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Post|null $post
 */
class Postmeta extends Model
{
    use HasFactory, SoftDeletes;

    public $guarded = [];

    public function post():BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
