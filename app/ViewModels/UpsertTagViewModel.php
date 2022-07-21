<?php

namespace App\ViewModels;

use App\DataTransferObjects\TagData;
use App\Models\Tag;

class UpsertTagViewModel extends ViewModel
{
    public function __construct(
        public readonly ?Tag $tag = null
    ) {}

    public function tag(): ?TagData
    {
        if (!$this->tag) {
            return null;
        }

        return TagData::from($this->tag);
    }
}
