<?php

namespace App\Actions\Blog;

use App\Models\Tag;

class MassDeleteTagAction
{
    public static function execute(array $dataIds)
    {
        collect($dataIds)->each(function($id) {
            DeleteTagAction::execute(Tag::getEntityById($id));
        });
    }
}
