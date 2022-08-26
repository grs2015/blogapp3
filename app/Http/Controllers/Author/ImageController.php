<?php

namespace App\Http\Controllers\Author;

use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\ImageData;
use App\Actions\Blog\DeleteHeroImageAction;
use App\Actions\Blog\DeleteGalleryImageAction;

class ImageController extends Controller
{
    public function __construct(
        public ImageService $imageService
    ) {}

    public function delete_heroimage(ImageData $data, ImageService $imageService)
    {
        DeleteHeroImageAction::execute($data, $imageService);

        return redirect()->action([PostController::class, 'edit'], ['post' => $data->slug]);
    }

    public function delete_galleryimage(ImageData $data, ImageService $imageService)
    {
        DeleteGalleryImageAction::execute($data, $imageService);

        return redirect()->action([PostController::class, 'edit'], ['post' => $data->slug]);
    }
}
