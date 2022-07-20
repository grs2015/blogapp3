<?php

namespace App\ViewModels;

use App\Models\Post;
use App\Models\Postmeta;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PostmetaData;

class GetPostmetasViewModel extends ViewModel
{
    public function __construct(
        public readonly Post $post
    ) {  }

    public function postmetas(): ?Collection
    {
        return Postmeta::whereBelongsTo($this->post)
            ->get()
            ->map(fn(Postmeta $postmeta) => PostmetaData::from($postmeta));
    }
}
