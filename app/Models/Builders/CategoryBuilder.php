<?php

namespace App\Models\Builders;

class CategoryBuilder extends BaseBuilder
{
    public function detachPosts()
    {
        return $this->model->posts()->detach();
    }
}
