<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

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
