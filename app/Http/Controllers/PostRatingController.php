<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostRatingController extends Controller
{
    public function store(Post $post)
    {
        $post->rate(request('rating'));
    }
}
