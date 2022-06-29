<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface CategoryRepositoryInterface
{
    public function getAllEntries():?Collection;
    public function getEntryById(int $catId):?Model;
    public function deleteEntry(int $catId):int;
    public function createEntry(array $catAttributes):?Model;
    public function updateEntry(int $catId, array $newAttributes):?int;
}
