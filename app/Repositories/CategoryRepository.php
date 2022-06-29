<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\CategoryRepositoryInterface;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        protected Category $model
    ) {}

    /**
     * Gets all entries from tags table
     * @return Collection|null
     */
    public function getAllEntries(): ?Collection
    {
        return $this->model->all();
    }

    public function getEntryById(int $catId): ?Model
    {
        return $this->model->findOrFail($catId);
    }

    public function deleteEntry(int $catId): int
    {
        return $this->model->destroy($catId);
    }

    public function createEntry(array $catAttributes): ?Model
    {
        return $this->model->create($catAttributes);
    }

    public function updateEntry(int $catId, array $newAttributes): ?int
    {
        return $this->model->whereId($catId)->update($newAttributes);
    }
}
