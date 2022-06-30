<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {}

    public function getAllEntries(): ?Collection
    {
        return $this->model->all();
    }

    public function getEntryById(int $userId): ?Model
    {
        return $this->model->findOrFail($userId);
    }

    public function deleteEntry(int $userId):int
    {
        return $this->model->destroy($userId);
    }

    public function createEntry(array $userAttributes): ?Model
    {
        return $this->model->create($userAttributes);
    }

    public function updateEntry(int $userId, array $newAttributes): ?int
    {
        return $this->model->whereId($userId)->update($newAttributes);
    }


}
