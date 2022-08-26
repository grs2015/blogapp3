<?php

namespace App\Actions\Blog;

use App\Models\Baseinfo;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use App\DataTransferObjects\BaseinfoData;

class UpsertBaseinfoAction
{
    public static function execute(BaseinfoData $data, ImageService $imageService, ?Collection $files): bool
    {
        // if hero_image is not null and existing entry have to be replaced
        if ($files->has('hero_image')) {
            if (Baseinfo::find(1)->hero_image) {
                $imageService->deleteHeroImages(Baseinfo::find(1)->hero_image);
            }
            $imageService->generateNames($files->get('hero_image'));
            $data->hero_image = $imageService->storeThumbHeroImages([[640, 480]])
                    ->storeHeroImages(60)->generateHeroURL()->filenamesDB;
        }
        // If hero_image is null, but the post and entry exist and we don't want to overwrite entry with null
        // That's the case when updating galleries on the frontend
        if (!$files->has('hero_image') && Baseinfo::find(1)->hero_image) {
            $data->hero_image = Baseinfo::find(1)->hero_image;
        }

        return Baseinfo::find(1)->update($data->all());
    }
}
