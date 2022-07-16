<?php

namespace App\Actions\Blog;

use App\Models\Baseinfo;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\DataTransferObjects\BaseinfoData;
use Illuminate\Http\UploadedFile;

class UpsertBaseinfoAction
{
    public static function execute(BaseinfoData $data, ?UploadedFile $file, ImageService $imageService)
    {
        if ($data->hero_image) {
            if ($data->id) {
                $imageService->deleteHeroImages(Baseinfo::getEntityById($data->id)->hero_image);
            }
            $imageService->generateNames($file);
            $data->hero_image = $imageService->storeHeroImages()->generateHeroURL()->filenamesDB;
        }

        return Baseinfo::updateOrCreate(['id' => $data->id], $data->all());
    }
}
