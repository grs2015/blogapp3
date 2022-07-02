<?php

namespace App\Repositories;

use App\Models\Post;
use App\Interfaces\PublicPostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PublicPostRepository implements PublicPostRepositoryInterface
{
    public function __construct(
        protected Post $model
    ) {  }

    public function getAllEntries(): ?Collection
    {
        return $this->model->with(['comments', 'tags', 'categories', 'postmetas', 'user'])->get();
    }

    public function getEntryById(int $postId): ?Model
    {
        return $this->model->whereId($postId)->with(['comments', 'tags', 'categories', 'postmetas', 'user'])->firstOrFail();
    }
}
