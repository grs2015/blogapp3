<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Models\Baseinfo;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\DataTransferObjects\PostData;


class UpsertPostAction
{
    public static function execute(PostData $data, ImageService $imageService, ?Collection $files): Post
    {
        if ($files->has('hero_image')) {
            if ($data->id) {
                $imageService->deleteHeroImages(Post::getEntityById($data->id)->hero_image);
            }
            $imageService->generateNames($files->get('hero_image'));
            $data->hero_image = $imageService->storeThumbHeroImages([[100, 100], [200, 200], [640, 480]])
                    ->storeHeroImages()->generateHeroURL()->filenamesDB;
        }

        $post = Post::updateOrCreate(['id' => $data->id], $data->except('tags', 'categories', 'user')->all());

        if ($files->has('images'))  {
            $imageService->deleteGallery($post);
            $imageService->generateGallery($files->toArray(), $post, [[200, 200], [640, 480]]);
        }

        if ($data->tags) {
            $post->tags()->sync($data->tags->toCollection()->pluck('id'));
        }

        if ($data->categories) {
            $post->categories()->sync($data->categories->toCollection()->pluck('id'));
        }

        return $post;
    }
}
