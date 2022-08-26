<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Models\Gallery;
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
        // if hero_image is not null and existing entry have to be replaced
        if ($files->has('hero_image')) {
            if ($data->id && Post::getEntityById($data->id)->hero_image) {
                $imageService->deleteHeroImages(Post::getEntityById($data->id)->hero_image);
            }
            $imageService->generateNames($files->get('hero_image'));
            $data->hero_image = $imageService->storeThumbHeroImages([[640, 480]])
                    ->storeHeroImages(60)->generateHeroURL()->filenamesDB;
        }
        // If hero_image is null, but the post and entry exist and we don't want to overwrite entry with null
        // That's the case when updating galleries on the frontend
        if (!$files->has('hero_image') && $data->id && Post::getEntityById($data->id)->hero_image) {
            $data->hero_image = Post::getEntityById($data->id)->hero_image;
        }

        $post = Post::updateOrCreate(['id' => $data->id], $data->except('rating', 'comments_count', 'tags', 'categories', 'user')->all());

        if ($files->has('images'))  {
            $imageService->deleteGallery($post);
            Gallery::whereBelongsTo($post)->delete();
            $imageService->generateGallery($files->toArray(), $post, [[640, 480]]);
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
