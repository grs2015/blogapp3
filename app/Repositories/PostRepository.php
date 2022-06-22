<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    protected Model $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all entries
     * @param integer $userId
     * @return Collection|null
     */
    public function getAllEntries(int $userId):?Collection
    {
        return $this->model->where('id', $userId)->first()->posts;
    }

    /**
     * Get specific entry
     * @param integer $userId
     * @param integer $postId
     * @return Model|null
     */
    public function getEntryById(int $userId, int $postId): ?Model
    {
        return $this->model->where('id', $userId)->first()->posts()->where('id', $postId)->firstOrFail();
    }

    /**
     * Delete specific entry
     * @param integer $userId
     * @param integer $postId
     * @return void
     */
    public function deleteEntry(int $userId, int $postId): void
    {
        $this->model->where('id', $userId)->first()->posts()->where('id', $postId)->delete();
    }

    /**
     * Update specific entry
     * @param integer $userId
     * @param integer $postId
     * @param array $newAttributes
     * @return Model|null
     */
    public function updateEntry(int $userId, int $postId, array $newAttributes): ?Model
    {
        return $this->model->where('id', $userId)->first()->posts()->where('id', $postId)->update($newAttributes);
    }

    /**
     * Create new entry
     * @param integer $userId
     * @param array $postAttributes
     * @return Model|null
     */
    public function createEntry(int $userId, array $postAttributes):?Model
    {
        return $this->model->where('id', $userId)->first()->posts()->create($postAttributes);
    }
}
