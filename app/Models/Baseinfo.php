<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Baseinfo
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $title
 * @property string|null $content
 * @property string|null $meta_title
 * @property string|null $hero_image
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @method static \Database\Factories\BaseinfoFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereHeroImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Baseinfo whereWebsite($value)
 * @mixin \Eloquent
 */
class Baseinfo extends Model
{
    use HasFactory;

    public $guarded = [];
}
