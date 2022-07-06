<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    private UploadedFile $file;
    private string $timestamp;
    private string $filenameWithExtension;
    private string $filename;
    private Collection $filenames;

    public function __construct(
        public ?Model $model = null
    ) {}

    public function generateNames(UploadedFile $file)
    {
        $this->file = $file;
        $this->timestamp = now()->format('Y-m-d-H-i-s');
        $this->filenameWithExtension = "{$this->timestamp}-{$this->file->getClientOriginalName()}";
        $this->filename = pathinfo($this->filenameWithExtension)['filename'];
        $this->filenames = collect([]);
    }

    public function deleteHeroImages(string $pathToImages):bool
    {
        $namesArr = explode(',', $pathToImages);
        return Storage::disk('public')->delete($namesArr);
    }

    public function storeHeroImages(?int $width = null, ?int $height = 600, ?int $quality = 100):self
    {
        $fileHiRes = Image::make($this->file);
        $fileLoRes = $fileHiRes->fit($width, $height, function($constraint) { $constraint->upsize(); });
        Storage::put("uploads/HiRes-{$this->filename}.{$this->file->getClientOriginalExtension()}", $fileHiRes->stream('jpg', 100));
        Storage::put("uploads/LoRes-{$this->filename}.{$this->file->getClientOriginalExtension()}", $fileLoRes->stream('jpg', $quality));

        return $this;
    }

    public function generateHeroURL():string
    {
        $urlLoRes = str_replace('/storage/', '', Storage::url("uploads/LoRes-{$this->filename}.{$this->file->getClientOriginalExtension()}"));
        $urlHiRes = str_replace('/storage/', '', Storage::url("uploads/HiRes-{$this->filename}.{$this->file->getClientOriginalExtension()}"));
        $this->filenames->push($urlHiRes, $urlLoRes);
        $filenamesDB = $this->filenames->implode(',');

        return $filenamesDB;
    }

    public function storeThumbImages()
    {

    }
}
