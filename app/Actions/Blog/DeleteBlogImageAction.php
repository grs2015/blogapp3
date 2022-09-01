<?php

namespace App\Actions\Blog;

use App\Models\Baseinfo;
use App\Services\ImageService;

class DeleteBlogImageAction
{
    public static function execute(ImageService $imageService): void
    {
        $info = Baseinfo::find(1);

        if ($info->hero_image) {
            $imageService->deleteHeroImages($info->hero_image);
        }

        $info->hero_image = null;
        $info->save();
    }
}
