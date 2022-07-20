<?php

namespace App\ViewModels;

use App\DataTransferObjects\CategoryData;
use App\Models\Category;

class UpsertCategoryViewModel extends ViewModel
{
    public function __construct(
        public readonly ?Category $category = null
    ) {  }

    public function category(): ?CategoryData
    {
        if (!$this->category) {
            return null;
        }

        return CategoryData::from($this->category);
    }
}
