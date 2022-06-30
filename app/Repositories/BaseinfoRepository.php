<?php

namespace App\Repositories;

use App\Models\Baseinfo;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\BaseinfoRepositoryInterface;


class BaseinfoRepository implements BaseinfoRepositoryInterface
{
    public function __construct(
        protected Baseinfo $model
    ) {}

    /**
     * Gets all entries from tags table
     * @return Collection|null
     */
    public function getAllEntries(): ?Collection
    {
        return $this->model->all();
    }

    public function getEntryById(int $baseId): ?Model
    {
        return $this->model->findOrFail($baseId);
    }

    public function deleteEntry(int $baseId): int
    {
        return $this->model->destroy($baseId);
    }

    public function createEntry(array $baseAttributes): ?Model
    {
        return $this->model->create($baseAttributes);
    }

    public function updateEntry(int $baseId, array $newAttributes): ?int
    {
        return $this->model->whereId($baseId)->update($newAttributes);
    }
}
