<?php

namespace App\Http\Controllers\Author;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {  }


    public function index()
    {
        $cats = $this->categoryRepository->getAllEntries();

        return view('author.category.index', ['cats' => $cats]);
    }

    public function show(Category $category)
    {
        $cat = $this->categoryRepository->getEntryById($category->id);

        return view('author.category.show', ['category' => $cat]);
    }
}
