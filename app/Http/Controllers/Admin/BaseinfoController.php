<?php

namespace App\Http\Controllers\Admin;

use App\Models\Baseinfo;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreBaseinfoRequest;
use App\Http\Requests\UpdateBaseinfoRequest;
use App\Interfaces\BaseinfoRepositoryInterface;
use App\Services\CacheService;

class BaseinfoController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {  }

    public function index(CacheService $cacheService)
    {
        $infos = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() {
            return Baseinfo::all();
        });

        return view('baseinfo.index', compact(['infos']));
    }

    public function create()
    {
        return view('baseinfo.create');
    }

    public function store(StoreBaseinfoRequest $request)
    {
        $validated = $request->safe()->except(['hero_image']);

        if ($request->has('hero_image')) {
            $this->imageService->generateNames($request->file('hero_image'));
            $validated['hero_image'] = $this->imageService->storeHeroImages()->generateHeroURL()->filenamesDB;
        }

        Baseinfo::createBaseinfo($validated);

        return redirect()->action([BaseinfoController::class, 'index']);
    }

    public function show(Baseinfo $baseinfo, CacheService $cacheService)
    {
        $info = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() use ($baseinfo) {
            return Baseinfo::getBaseinfoById($baseinfo->id);
        });

        return view('baseinfo.show', compact(['info']));
    }

    public function edit(Baseinfo $baseinfo)
    {
        $info = Baseinfo::getBaseinfoById($baseinfo->id);

        return view('baseinfo.edit', compact(['info']));
    }

    public function update(UpdateBaseinfoRequest $request, Baseinfo $baseinfo)
    {
        $validated = $request->safe()->except(['hero_image']);

        if ($request->has('hero_image')) {

            $this->imageService->deleteHeroImages($baseinfo->hero_image);
            $this->imageService->generateNames($request->file('hero_image'));
            $validated['hero_image'] = $this->imageService->storeHeroImages()->generateHeroURL()->filenamesDB;
        }

        Baseinfo::updateBaseinfo($baseinfo->id, $validated);

        return redirect()->action([BaseinfoController::class, 'edit'], ['baseinfo' => $baseinfo->id]);
    }

    public function destroy(Baseinfo $baseinfo)
    {
        Baseinfo::destroyBaseinfo($baseinfo->id);

        return redirect()->action([BaseinfoController::class, 'index']);
    }
}
