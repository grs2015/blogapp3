<?php

namespace App\Actions\Blog;

use App\Models\Category;
use App\Events\CategoryCreated;
use App\Events\CategoryUpdated;
use App\DataTransferObjects\CategoryData;

class UpsertCategoryAction
{
    public static function execute(CategoryData $data): Category
    {
        $category = Category::updateOrCreate(['id' => $data->id], $data->all());

        // $data->id ?
        // CategoryUpdated::dispatch($data->title, $data->content ?? 'No content provided') :
        // CategoryCreated::dispatch($data->title, $data->content ?? 'No content provided');

        return $category;
    }
}
