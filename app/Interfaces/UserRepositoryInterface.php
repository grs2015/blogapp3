<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface
{
    public function getAllEntries():?Collection;
    public function getEntryById(int $userId):?Model;
    public function deleteEntry(int $userId):int;
    public function createEntry(array $userAttributes):?Model;
    public function updateEntry(int $userId, array $newAttributes):?int;
}
