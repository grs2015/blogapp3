<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface PostmetaRepositoryInterface
{
    public function getAllEntries(int $postId):?Collection;
    public function getEntryById(int $postId, int $postmetaId):?Model;
    public function deleteEntry(int $postId, int $postmetaId):void;
    public function createEntry(int $postId, array $postmetaAttributes):?Model;
    public function updateEntry(int $postId, int $postmetaId, array $newAttributes):?int;
}
