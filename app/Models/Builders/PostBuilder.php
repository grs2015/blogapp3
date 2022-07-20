<?php

namespace App\Models\Builders;

use App\Enums\PostStatus;
use App\Enums\FavoriteStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    public function restoreTrashed(array $ids): void
    {
        $this->onlyTrashed()->whereIn('id', $ids)->restore();
    }

    public function getTrashedCollection(array $ids): ?Collection
    {
        return $this->onlyTrashed()->whereIn('id', $ids)->get();
    }
}
