<?php

namespace App\ViewModels;

use App\DataTransferObjects\PostmetaData;
use App\Models\Postmeta;

class UpsertPostmetaViewModel extends ViewModel
{
    public function __construct(
        public readonly ?Postmeta $postmeta = null
    ) {}

    public function postmeta(): ?PostmetaData
    {
        if (!$this->postmeta) {
            return null;
        }

        return PostmetaData::from($this->postmeta);
    }
}

