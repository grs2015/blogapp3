<?php

namespace App\Http\Controllers\Member;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class PostRatingController extends Controller
{
    public function store(Request $request, Post $post)
    {

        $request->validate([
            'rating' => ['required', 'integer', Rule::in([1,2,3,4,5])]
        ]);

        $post->rate(request('rating'), $request->user());
    }
}
