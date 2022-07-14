<?php

namespace App\Models\Builders;

class CategoryBuilder extends BaseBuilder
{
    public function detachPosts(int $entityId)
    {
        return $this->where('id', $entityId)->first()->posts()->detach();
    }
}
