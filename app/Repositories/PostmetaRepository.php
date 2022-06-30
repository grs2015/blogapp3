<?php

namespace App\Repositories;


use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\PostmetaRepositoryInterface;



class CommentRepository implements PostmetaRepositoryInterface
{
    protected Model $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all entries of current user
     * @param integer $userId
     * @return Collection|null
     */
    public function getAllEntries(int $postId):?Collection
    {
        return $this->model->whereId($postId)->first()->postmetas;
    }

    /**
     * Get specific entry of current user
     * @param integer $userId
     * @param integer $postId
     * @return Model|null
     */
    public function getEntryById(int $postId, int $postmetaId): ?Model
    {
        return $this->model->whereId($postId)->first()->postmetas()->whereId($postmetaId)->firstOrFail();
    }

    /**
     * Delete specific entry of current user
     * @param integer $userId
     * @param integer $postId
     * @return void
     */
    public function deleteEntry(int $postId, int $postmetaId): void
    {
        $this->model->whereId($postId)->first()->postmetas()->whereId($postmetaId)->delete();
    }

    /**
     * Update specific entry of current user
     * @param integer $userId
     * @param integer $postId
     * @param array $newAttributes
     * @return Model|null
     */
    public function updateEntry(int $postId, int $postmetaId, array $newAttributes): ?int
    {
        return $this->model->whereId($postId)->first()->postmetas()->whereId($postmetaId)->update($newAttributes);
    }

    /**
     * Create new entry for current user
     * @param integer $userId
     * @param array $postAttributes
     * @return Model|null
     */
    public function createEntry(int $postId, array $postmetaAttributes):?Model
    {
        return $this->model->whereId($postId)->first()->postmetas()->create($postmetaAttributes);
    }
}
