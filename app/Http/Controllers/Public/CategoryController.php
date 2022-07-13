<?php

namespace App\Http\Controllers\Public;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {  }

    public function index()
    {
        $cats = $this->categoryRepository->getAllEntries();

        return view('public.category.index', ['cats' => $cats]);
    }

    public function show(Category $category)
    {
        $cat = $this->categoryRepository->getEntryById($category->id);

        return view('public.category.show', ['category' => $cat]);
    }
}
