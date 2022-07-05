<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function post():BelongsTo
    {
        return $this->belongsTo(Post::class, 'author_id');
    }
}
