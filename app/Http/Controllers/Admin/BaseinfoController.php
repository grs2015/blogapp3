<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Baseinfo;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\ViewModels\GetBaseinfoViewModel;
use App\DataTransferObjects\BaseinfoData;
use App\Actions\Blog\UpsertBaseinfoAction;
use App\ViewModels\UpsertBaseinfoViewModel;

class BaseinfoController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {  }

    public function index(CacheService $cacheService)
    {
        return Inertia::render('Baseinfo/Index', [
            'model' => new GetBaseinfoViewModel($cacheService)
        ]);
    }

    public function create()
    {
        return Inertia::render('Baseinfo/Form', [
            'model' => new UpsertBaseinfoViewModel()
        ]);
    }

    public function edit(Baseinfo $baseinfo)
    {
        return Inertia::render('Baseinfo/Form', [
            'model' => new UpsertBaseinfoViewModel($baseinfo)
        ]);
    }

    public function show(Baseinfo $baseinfo)
    {
        return Inertia::render('Baseinfo/Show', [
            'model' => new UpsertBaseinfoViewModel($baseinfo)
        ]);
    }

    public function store(BaseinfoData $data, Request $request, ImageService $imageService)
    {
        UpsertBaseinfoAction::execute($data, $request->file('hero_image'), $imageService);

        return redirect()->action([BaseinfoController::class, 'index']);
    }

    public function update(BaseinfoData $data, Request $request, ImageService $imageService)
    {
        UpsertBaseinfoAction::execute($data, $request->file('hero_image'), $imageService);

        return redirect()->action([BaseinfoController::class, 'edit'], ['baseinfo' => $data->id]);
    }

    public function destroy(Baseinfo $baseinfo, ImageService $imageService)
    {
        if ($baseinfo->hero_image) {
            $imageService->deleteHeroImages(Baseinfo::getEntityById($baseinfo->id)->hero_image);
        }

        Baseinfo::destroyEntity($baseinfo->id);

        return redirect()->action([BaseinfoController::class, 'index']);
    }
}
