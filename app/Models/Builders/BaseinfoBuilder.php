<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BaseinfoBuilder extends Builder
{
    public function getBaseinfoById(int $id): ?Model
    {
        return $this->where('id', $id)->first();
    }

    public function updateBaseinfo(int $baseId, array $newAttributes): ?int
    {
        return $this->where('id', $baseId)->update($newAttributes);
    }

    public function createBaseinfo(array $baseAttributes): Model
    {
        return $this->create($baseAttributes);
    }

    public function destroyBaseinfo(int $baseId): int
    {
        return $this->where('id', $baseId)->delete();
    }
}
