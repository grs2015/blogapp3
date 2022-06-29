<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface CommentRepositoryInterface
{
    public function getAllEntries(int $postId):?Collection;
    public function getEntryById(int $postId, int $commentId):?Model;
    public function deleteEntry(int $postId, int $commentId):void;
    public function createEntry(int $postId, array $commentAttributes):?Model;
    public function updateEntry(int $postId, int $commentId, array $newAttributes):?int;
}
