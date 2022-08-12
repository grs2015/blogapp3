<?php

namespace App\Actions\Blog;

use App\Models\Tag;
use App\Events\TagDeleted;

class DeleteTagAction
{
    public static function execute(Tag $tag)
    {
        $tag->detachPosts();
        $tag->delete();

        // TagDeleted::dispatch($tag->title, $tag->content ?? 'No content provided');
    }
}
