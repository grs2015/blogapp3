<?php

namespace App\Http\Controllers;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {  }


    public function index()
    {
        $cats = $this->categoryRepository->getAllEntries();

        return view('category.index', ['cats' => $cats]);
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show(Category $category)
    {

    }

    public function edit(Category $category)
    {

    }

    public function update(Request $request, Category $category)
    {

    }

    public function destroy(Category $category)
    {

    }
}
