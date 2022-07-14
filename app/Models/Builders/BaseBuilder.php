<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BaseBuilder extends Builder
{
    public function getEntityById(int $entityId): ?Model
    {
        return $this->where('id', $entityId)->first();
    }

    public function updateEntity(int $entityId, array $newAttributes): ?int
    {
        return $this->where('id', $entityId)->update($newAttributes);
    }

    public function createEntity(array $baseAttributes): Model
    {
        return $this->create($baseAttributes);
    }

    public function destroyEntity(int $entityId): int
    {
        return $this->where('id', $entityId)->delete();
    }
}
