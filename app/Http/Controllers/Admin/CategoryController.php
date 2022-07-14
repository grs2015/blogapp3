<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;
use App\Services\CacheService;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {  }


    public function index(CacheService $cacheService)
    {
        $cats = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() {
            return Category::all();
        });

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

        Category::createEntity($validated);

        CategoryCreated::dispatch($title, $content);

        return redirect()->action([CategoryController::class, 'index']);
    }

    public function show(Category $category, CacheService $cacheService)
    {
        $cat = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() use ($category){
            return Category::getEntityById($category->id);
        });

        return view('category.show', ['category' => $cat]);
    }

    public function edit(Category $category)
    {
        $cat = Category::getEntityById($category->id);

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

        Category::updateEntity($category->id, $validated);

        $category->refresh();

        CategoryUpdated::dispatch($title, $content);

        return redirect()->action([CategoryController::class, 'edit'], ['category' => $category->slug]);
    }

    public function destroy(Category $category)
    {
        $title = $category->title;
        $content = $category->content ?? 'No content provided';

        Category::detachPosts($category->id);
        Category::destroyEntity($category->id);

        // $category->posts()->detach();

        // $this->categoryRepository->deleteEntry($category->id);

        CategoryDeleted::dispatch($title, $content);

        return redirect()->action([CategoryController::class, 'index']);
    }
}
