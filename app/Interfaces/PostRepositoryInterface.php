<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface PostRepositoryInterface
{
    public function getAllEntries(int $userId):?Collection;
    public function getEntryById(int $userId, int $postId):?Model;
    public function deleteEntry(int $userId, int $postId):void;
    public function createEntry(int $userId, array $postAttributes):?Model;
    public function updateEntry(int $userId, int $postId, array $newAttributes):?int;
}
