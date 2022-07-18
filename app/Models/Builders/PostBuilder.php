<?php

namespace App\Models\Builders;

use App\Enums\PostStatus;
use App\Enums\FavoriteStatus;

class PostBuilder extends BaseBuilder
{
    public function markAsPending(): void
    {
        $this->model->status = PostStatus::Pending;
        $this->model->save();
    }

    public function markAsPublished(): void
    {
        $this->model->status = PostStatus::Published;
        $this->model->save();
    }

    public function markAsDraft(): void
    {
        $this->model->status = PostStatus::Draft;
        $this->model->save();
    }

    public function markAsUnpublished(): void
    {
        $this->model->status = PostStatus::Unpublished;
        $this->model->save();
    }

    public function markAsFavorite(): void
    {
        $this->model->status = FavoriteStatus::Favorite;
        $this->model->save();
    }

    public function markAsNonFavorite(): void
    {
        $this->model->status = FavoriteStatus::Nonfavorite;
        $this->model->save();
    }
}
