<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface TagRepositoryInterface
{
    public function getAllEntries():?Collection;
    public function getEntryById(int $tagId):?Model;
    public function deleteEntry(int $tagId):int;
    public function createEntry(array $postAttributes):?Model;
    public function updateEntry(int $tagId, array $newAttributes):?int;
}
