<?php

namespace App\ViewModels;

use App\Models\Post;
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
}
