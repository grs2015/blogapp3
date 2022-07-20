<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\DataTransferObjects\ForceDeletePostData;
use App\Services\ImageService;

class ForceDeletePostAction
{
    public static function execute(ForceDeletePostData $data, ImageService $imageService)
    {
        Post::getTrashedCollection($data->ids)->each(function($post) use ($imageService) {
            if ($post->hero_image) {
                $imageService->deleteHeroImages($post->hero_image);
            }

            $imageService->deleteGallery($post);
            $post->tags()->detach();
            $post->categories()->detach();
            $post->forceDelete();
        });
    }
}
