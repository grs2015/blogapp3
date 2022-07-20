<?php

namespace App\Actions\Blog;

use App\Models\Category;
use App\Events\CategoryDeleted;

class DeleteCategoryAction
{
    public static function execute(Category $category)
    {
        $category->detachPosts();
        $category->delete();

        CategoryDeleted::dispatch($category->title, $category->content ?? 'No content provided');
    }
}
