<?php

namespace App\Http\Controllers\Member;

use App\Models\Post;
use App\Http\Controllers\Controller;

class PostRatingController extends Controller
{
    public function store(Post $post)
    {
        request()->validate([
            'rating' => ['required', 'in:1,2,3,4,5']
        ]);

        $post->rate(request('rating'));
    }

    //TODO - Frontend part should be added later
}
