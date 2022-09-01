<?php

namespace App\ViewModels;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PostData;

class GetSinglePostViewModel extends ViewModel
{
    public function __construct(
        public readonly Post $post
    ) {}

    public function post(): PostData
    {
        return PostData::from($this->post->load('galleries', 'tags', 'categories', 'user', 'postmetas'));
    }

    // public function tags(): Collection
    // {
    //     return Tag::all()->map->getData();
    // }

    // public function categories(): Collection
    // {
    //     return Category::all()->map->getData();
    // }
}
