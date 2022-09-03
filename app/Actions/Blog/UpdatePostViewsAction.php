<?php

namespace App\Actions\Blog;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class UpdatePostViewsAction
{
    public static function execute(Post $post): Model
    {
        if (!in_array($post->id, session()->get('viewed_posts', []) )) {
            if (!$post['views']) {
                $post['views'] = 0;
            }
            $post->increment('views');
            session()->push('viewed_posts', $post->id);
            $post->save();
        }

        return $post;
    }
}
