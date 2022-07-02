<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface PublicPostRepositoryInterface
{
    public function getAllEntries():?Collection;
    public function getEntryById(int $postId):?Model;
}
