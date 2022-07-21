<?php

namespace App\ViewModels;

use App\Models\Tag;
use Illuminate\Support\Collection;

class GetTagsViewModel extends ViewModel
{
    public function tags(): Collection
    {
        return Tag::all()->map->getData();
    }
}
