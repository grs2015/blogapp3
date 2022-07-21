<?php

namespace App\Models\Builders;

class TagBuilder extends BaseBuilder
{
    public function detachPosts()
    {
        return $this->model->posts()->detach();
    }
}
