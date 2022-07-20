<?php

namespace App\ViewModels;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Collection;
use App\DataTransferObjects\TagData;
use App\DataTransferObjects\PostData;

class UpsertPostViewModel extends ViewModel
{
    public function __construct(
        public readonly ?Post $post = null
    ) {}

    public function post(): ?PostData
    {
        if (!$this->post) {
            return null;
        }

        return PostData::from($this->post->load('galleries', 'tags', 'categories', 'user', 'postmetas'));
    }

    public function tags(): Collection
    {
        return Tag::all()->map->getData();
    }

    public function categories(): Collection
    {
        return Category::all()->map->getData();
    }
}
