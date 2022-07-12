<?php

namespace App\Http\Controllers\Public;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments', 'postmetas'])->get();

        return view('public.post.index', compact(['posts']));
    }

    public function show(Post $post)
    {
        $post = Post::where('author_id', $post->author_id)->where('id', $post->id)->firstOrFail();

        if (!in_array($post->id, session()->get('viewed_posts', []) )) {
            $post->increment('views');
            session()->push('viewed_posts', $post->id);
        }

        $views = Post::whereId($post->id)->first()->views;

        return view('public.post.show', ['post' => $post, 'user' => $post->user, 'views' => $views ]);
    }
}
