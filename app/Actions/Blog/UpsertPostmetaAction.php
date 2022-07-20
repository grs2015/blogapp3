<?php

namespace App\Actions\Blog;

use App\Models\Postmeta;
use App\DataTransferObjects\PostmetaData;

class UpsertPostmetaAction
{
    public static function execute(PostmetaData $data): Postmeta
    {
        return Postmeta::updateOrCreate(['id' => $data->id], $data->all());
    }
}
