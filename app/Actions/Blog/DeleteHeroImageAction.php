<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Services\ImageService;
use App\DataTransferObjects\ImageData;

class DeleteHeroImageAction {

    public static function execute(ImageData $data, ImageService $imageService): void
    {
        $post = Post::getEntityById($data->post_id);

        if ($post->hero_image) {
            $imageService->deleteHeroImages($post->hero_image);
        }

        $post->hero_image = null;
        $post->save();
    }
}
