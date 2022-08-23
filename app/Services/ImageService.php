<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\File;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    private UploadedFile|File $file;
    private array $galleryFiles;
    private string $timestamp;
    private string $filenameWithExtension;
    private string $filename;
    private Collection $filenames;
    private Collection $thumbFilenames;
    private Collection $avatarFilenames;
    public string $urlLoRes;
    public string $urlHiRes;
    public string $filenamesDB;
    public string $thumbFilenamesDB;
    public string $avatarFilenamesDB;

    public function __construct(
    ) {  }

    // public static function

    public function generateNames(UploadedFile|File $file)
    {
        $this->file = $file;
        $this->timestamp = now()->format('Y-m-d-H-i-s');
        $this->filenameWithExtension = "{$this->timestamp}-{$this->file->getClientOriginalName()}";
        $this->filename = pathinfo($this->filenameWithExtension)['filename'];
        $this->filenames = collect([]);
        $this->thumbFilenames = collect([]);
        $this->avatarFilenames = collect([]);
    }

    public function deleteAvatarImage(string $pathToImage):bool
    {
        $clippedPath = substr($pathToImage, strpos($pathToImage, 'uploads'));

        return Storage::disk('public')->delete($clippedPath);
    }

    public function deleteHeroImages(string $pathToImages):bool
    {
        $namesArr = explode(',', $pathToImages);
        $clippedPaths = Arr::map($namesArr, function($item) {
            return substr($item, strpos($item, 'uploads'));
        });

        return Storage::disk('public')->delete($clippedPaths);
    }

    public function deleteGallery(Post $post):void
    {
        $postGallery = $post->galleries()->get();
        if ($postGallery->isEmpty()) {
            return;
        }
        $postGallery->each(function($gallery) {
            $namesArr = [ $gallery->original, $gallery->lowres, explode(',', $gallery->thumbs)[0]];
            $clippedPaths = Arr::map($namesArr, function($item) {
                return substr($item, strpos($item, 'uploads'));
            });
            Storage::disk('public')->delete($clippedPaths);
        });
    }

    public function deleteGalleryImage(Post $post, int $index): void
    {
        // $postGalleryImage = ($post->galleries()->get()->toArray())[$index];
        $postGalleryImage = $post->galleries()->get()[$index];

        $namesArr = [ $postGalleryImage->original, $postGalleryImage->lowres, explode(',', $postGalleryImage->thumbs)[0]];
        $clippedPaths = Arr::map($namesArr, function($item) {
            return substr($item, strpos($item, 'uploads'));
        });
        Storage::disk('public')->delete($clippedPaths);
    }

    public function storeHeroImages(?int $quality = 100, ?int $width = 600, ?int $height = 600):self
    {
        $fileHiRes = Image::make($this->file);
        Storage::put("uploads/HiRes-{$this->filename}.{$this->file->getClientOriginalExtension()}", $fileHiRes->stream('jpg', 100));
        // $fileLoRes = $fileHiRes;
        Storage::put("uploads/LoRes-{$this->filename}.{$this->file->getClientOriginalExtension()}", $fileHiRes->stream('jpg', $quality));

        return $this;
    }

    public function generateHeroURL(): self
    {
        // $this->urlLoRes = str_replace('/storage/', '', Storage::url("uploads/LoRes-{$this->filename}.{$this->file->getClientOriginalExtension()}"));
        // $this->urlHiRes = str_replace('/storage/', '', Storage::url("uploads/HiRes-{$this->filename}.{$this->file->getClientOriginalExtension()}"));
        $this->urlLoRes = Storage::url("uploads/LoRes-{$this->filename}.{$this->file->getClientOriginalExtension()}");
        $this->urlHiRes = Storage::url("uploads/HiRes-{$this->filename}.{$this->file->getClientOriginalExtension()}");
        $this->filenames->push($this->urlHiRes, $this->urlLoRes);
        $this->filenamesDB = $this->filenames->implode(',');
        $this->thumbFilenamesDB = $this->thumbFilenames->implode(',');

        return $this;
    }

    public function storeAvatarImage(array $dimensions): self
    {
        $dims = collect($dimensions);
        $dims->each(function($item) {
            $image = Image::make($this->file)->resize($item[0], $item[1], function ($constraint) {
                $constraint->aspectRatio();
            });

            Storage::put("uploads/avatar/{$item[0]}-{$item[1]}-{$this->filename}.{$this->file->getClientOriginalExtension()}", $image->stream());

            $url = Storage::url("uploads/avatar/{$item[0]}-{$item[1]}-{$this->filename}.{$this->file->getClientOriginalExtension()}");
            $this->avatarFilenames->push($url);
        });

        $this->avatarFilenamesDB = $this->avatarFilenames->implode(',');

        return $this;
    }

    public function storeThumbHeroImages(array $dimensions):self
    {
        $dims = collect($dimensions);
        $dims->each(function($item) {
            $image = Image::make($this->file)->resize($item[0], $item[1], function ($constraint) {
                $constraint->aspectRatio();
            });

            Storage::put("uploads/{$item[0]}-{$item[1]}-{$this->filename}.{$this->file->getClientOriginalExtension()}", $image->stream());

            // $url = str_replace('/storage/', '', Storage::url("uploads/{$item[0]}-{$item[1]}-{$this->filename}.{$this->file->getClientOriginalExtension()}"));
            $url = Storage::url("uploads/{$item[0]}-{$item[1]}-{$this->filename}.{$this->file->getClientOriginalExtension()}");
            $this->filenames->push($url);
            $this->thumbFilenames->push($url);
        });

        return $this;
    }

    public function generateGallery(array $files, Post $post, array $dimensions)
    {
        $this->galleryFiles = $files;

        collect($this->galleryFiles['images'])->each(function($file) use ($post, $dimensions) {

            $this->generateNames($file);
            $this->storeThumbHeroImages($dimensions)->storeHeroImages()->generateHeroURL();

            $imagesData = [
                'original' => $this->urlHiRes,
                'lowres' => $this->urlLoRes,
                'thumbs' => $this->thumbFilenamesDB
            ];

            $post->galleries()->create($imagesData);
        });
    }
}
