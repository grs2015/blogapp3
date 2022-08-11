<?php

namespace App\Actions\Blog;

use App\Models\Category;

class MassDeleteCategoryAction
{
    public static function execute(array $dataIds)
    {
        collect($dataIds)->each(function($id) {
            DeleteCategoryAction::execute(Category::getEntityById($id));
        });
    }
}
