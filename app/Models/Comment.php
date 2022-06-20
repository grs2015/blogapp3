<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    const PUBLISHED = 'published';
    const PENDING = 'pending';
    const UNPUBLISHED = 'unpublished';
}
