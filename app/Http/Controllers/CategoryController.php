<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\CategoryCreated;
use App\Events\CategoryUpdated;
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

        return view('category.index', ['cats' => $cats]);
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $title = $validated['title'];
        $content = $validated['content'] ?? 'No content provided';

        $this->categoryRepository->createEntry($validated);

        CategoryCreated::dispatch($title, $content);

        return redirect()->action([CategoryController::class, 'index']);
    }

    public function show(Category $category)
    {
        $cat = $this->categoryRepository->getEntryById($category->id);

        return view('category.show', ['category' => $cat]);
    }

    public function edit(Category $category)
    {
        $cat = $this->categoryRepository->getEntryById($category->id);

        return view('category.edit', ['category' => $cat]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        if ($request->has('title')) {
            $validated['slug'] = Str::slug($validated['title'], '-');
        }

        $title = $validated['title'];
        $content = $validated['content'] ?? 'No content provided';

        $this->categoryRepository->updateEntry($category->id, $validated);
        $category->refresh();

        CategoryUpdated::dispatch($title, $content);

        return redirect()->action([CategoryController::class, 'edit'], ['category' => $category->slug]);
    }

    public function destroy(Category $category)
    {

    }
}
