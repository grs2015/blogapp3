<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 */
class Postmeta extends Model
{
    use HasFactory;
}
