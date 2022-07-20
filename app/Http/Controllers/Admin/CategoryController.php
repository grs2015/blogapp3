<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Services\CacheService;
use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\DataTransferObjects\CategoryData;
use App\Actions\Blog\DeleteCategoryAction;
use App\Actions\Blog\UpsertCategoryAction;
use App\ViewModels\GetCategoriesViewModel;
use App\Http\Requests\StoreCategoryRequest;
use App\ViewModels\UpsertCategoryViewModel;
use App\Http\Requests\UpdateCategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    public function index(CacheService $cacheService)
    {
        return Inertia::render('Category/Index', [
            'model' => new GetCategoriesViewModel($cacheService)
        ]);
    }

    public function create()
    {
        return Inertia::render('Category/Form', [
            'model' => new UpsertCategoryViewModel()
        ]);

        return view('category.create');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Category/Form', [
            'model' => new UpsertCategoryViewModel($category)
        ]);
    }

    public function show(Category $category)
    {
        return Inertia::render('Category/Form', [
            'model' => new UpsertCategoryViewModel($category)
        ]);
    }

    public function store(CategoryData $data)
    {
        UpsertCategoryAction::execute($data);

        return redirect()->action([CategoryController::class, 'index']);
    }

    public function update(CategoryData $data)
    {
        UpsertCategoryAction::execute($data);

        return redirect()->action([CategoryController::class, 'edit'], ['category' => $data->slug]);
    }

    public function destroy(Category $category)
    {
        DeleteCategoryAction::execute($category);

        return redirect()->action([CategoryController::class, 'index']);
    }
}
