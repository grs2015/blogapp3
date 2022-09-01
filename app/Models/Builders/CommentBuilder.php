<?php

namespace App\Models\Builders;

use App\Enums\CommentStatus;
use App\Models\Builders\BaseBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CommentBuilder extends BaseBuilder
{
    public function getAllCommentEntities(int $postId): ?Collection
    {
        return $this->where('post_id', $postId)->get();
    }

    public function createCommentEntity(int $postId, array $commentAttributes): ?Model
    {
        return $this->create([...$commentAttributes, 'post_id' => $postId]);
    }

    public function getCommentEntityById(int $postId, int $commentId): ?Model
    {
        return $this->where('post_id', $postId)->where('id', $commentId)->firstOrFail();
    }

    public function markAsUnpublished(): void
    {
        $this->model->status = CommentStatus::Unpublished;
        $this->model->save();
    }

    public function markAsPublished(): void
    {
        $this->model->status = CommentStatus::Published;
        // $this->model->published_at = now()->toDateString();
        $this->model->save();
    }

    public function markAsPending(): void
    {
        $this->model->status = CommentStatus::Pending;
        $this->model->save();
    }
    // public function update
}
