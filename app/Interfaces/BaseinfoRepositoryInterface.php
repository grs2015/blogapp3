<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseinfoRepositoryInterface
{
    public function getAllEntries():?Collection;
    public function getEntryById(int $baseId):?Model;
    public function deleteEntry(int $baseId):int;
    public function createEntry(array $baseAttributes):?Model;
    public function updateEntry(int $baseId, array $newAttributes):?int;
}
