<?php

namespace App\ViewModels;

use App\DataTransferObjects\BaseinfoData;
use App\Models\Baseinfo;

class UpsertBaseinfoViewModel extends ViewModel
{
    public function __construct(
        public readonly ?Baseinfo $baseinfo = null
    ) {}

    public function baseinfo(): ?BaseinfoData
    {
        if (!$this->baseinfo) {
            return null;
        }

        return BaseinfoData::from($this->baseinfo);
    }
}
