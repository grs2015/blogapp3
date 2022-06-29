<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\TagRepositoryInterface;

class TagRepository implements TagRepositoryInterface
{
    public function __construct(
        protected Tag $model
    ) {}

    /**
     * Gets all entries from tags table
     * @return Collection|null
     */
    public function getAllEntries(): ?Collection
    {
        return $this->model->all();
    }

    public function getEntryById(int $tagId): ?Model
    {
        return $this->model->findOrFail($tagId);
    }

    public function deleteEntry(int $tagId): int
    {
        return $this->model->destroy($tagId);
    }

    public function createEntry(array $postAttributes): ?Model
    {
        return $this->model->create($postAttributes);
    }

    public function updateEntry(int $tagId, array $newAttributes): ?int
    {
        return $this->model->whereId($tagId)->update($newAttributes);
    }
}
