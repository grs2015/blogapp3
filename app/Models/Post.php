<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    const PUBLISHED = 'published';
    const DRAFT = 'draft';
    const PENDING = 'pending';
    const UNPUBLISHED = 'unpublished';

    const FAVORITE = 'favorite';
    const NONFAVORITE = 'usual';
}
