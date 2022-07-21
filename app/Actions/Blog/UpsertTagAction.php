<?php
namespace App\Actions\Blog;

use App\Models\Tag;
use App\Events\TagCreated;
use App\Events\TagUpdated;
use App\DataTransferObjects\TagData;

class UpsertTagAction
{
    public static function execute(TagData $data): Tag
    {
        $tag = Tag::updateOrCreate(['id' => $data->id], $data->all());

        $data->id ?
        TagUpdated::dispatch($data->title, $data->content ?? 'No content provided') :
        TagCreated::dispatch($data->title, $data->content ?? 'No content provided');

        return $tag;
    }
}
