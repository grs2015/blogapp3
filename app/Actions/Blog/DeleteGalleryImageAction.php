<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Services\ImageService;
use App\DataTransferObjects\ImageData;

class DeleteGalleryImageAction
{
    public static function execute(ImageData $data, ImageService $imageService): void
    {
        $post = Post::getEntityById($data->post_id);

        $imageService->deleteGalleryImage($post, $data->image_idx);

        $post->galleries()->get()[$data->image_idx]->delete();
    }
}
