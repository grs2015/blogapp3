<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\File;
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
    public string $urlLoRes;
    public string $urlHiRes;
    public string $filenamesDB;
    public string $thumbFilenamesDB;

    public function __construct(
    ) {  }

    public function generateNames(UploadedFile|File $file)
    {
        $this->file = $file;
        $this->timestamp = now()->format('Y-m-d-H-i-s');
        $this->filenameWithExtension = "{$this->timestamp}-{$this->file->getClientOriginalName()}";
        $this->filename = pathinfo($this->filenameWithExtension)['filename'];
        $this->filenames = collect([]);
        $this->thumbFilenames = collect([]);
    }

    public function deleteHeroImages(string $pathToImages):bool
    {
        $namesArr = explode(',', $pathToImages);
        return Storage::disk('public')->delete($namesArr);
    }

    public function deleteGallery(Post $post):void
    {
        $postGallery = $post->galleries()->get();
        if ($postGallery->isEmpty()) {
            return;
        }
        $postGallery->each(function($gallery) {
            $namesArr = [ $gallery->original, $gallery->lowres, explode(',', $gallery->thumbs)[0], explode(',', $gallery->thumbs)[1]];
            Storage::disk('public')->delete($namesArr);
        });
    }

    public function storeHeroImages(?int $width = null, ?int $height = 600, ?int $quality = 100):self
    {
        $fileHiRes = Image::make($this->file);
        $fileLoRes = $fileHiRes->fit($width, $height, function($constraint) { $constraint->upsize(); });
        Storage::put("uploads/HiRes-{$this->filename}.{$this->file->getClientOriginalExtension()}", $fileHiRes->stream('jpg', 100));
        Storage::put("uploads/LoRes-{$this->filename}.{$this->file->getClientOriginalExtension()}", $fileLoRes->stream('jpg', $quality));

        return $this;
    }

    public function generateHeroURL(): self
    {
        $this->urlLoRes = str_replace('/storage/', '', Storage::url("uploads/LoRes-{$this->filename}.{$this->file->getClientOriginalExtension()}"));
        $this->urlHiRes = str_replace('/storage/', '', Storage::url("uploads/HiRes-{$this->filename}.{$this->file->getClientOriginalExtension()}"));
        $this->filenames->push($this->urlHiRes, $this->urlLoRes);
        $this->filenamesDB = $this->filenames->implode(',');
        $this->thumbFilenamesDB = $this->thumbFilenames->implode(',');

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

            $url = str_replace('/storage/', '', Storage::url("uploads/{$item[0]}-{$item[1]}-{$this->filename}.{$this->file->getClientOriginalExtension()}"));
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
