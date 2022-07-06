<?php

namespace App\Http\Controllers;

use App\Models\Baseinfo;
use App\Http\Requests\StoreBaseinfoRequest;
use App\Http\Requests\UpdateBaseinfoRequest;
use App\Interfaces\BaseinfoRepositoryInterface;
use App\Services\ImageService;

class BaseinfoController extends Controller
{
    public function __construct(
        private BaseinfoRepositoryInterface $baseinfoRepository,
        private ImageService $imageService
    ) {  }

    public function index()
    {
        $infos = $this->baseinfoRepository->getAllEntries();

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
            $validated['hero_image'] = $this->imageService->storeHeroImages()->generateHeroURL();
        }

        $this->baseinfoRepository->createEntry($validated);

        return redirect()->action([BaseinfoController::class, 'index']);
    }

    public function show(Baseinfo $baseinfo)
    {
        $info = $this->baseinfoRepository->getEntryById($baseinfo->id);

        return view('baseinfo.show', compact(['info']));
    }

    public function edit(Baseinfo $baseinfo)
    {
        $info = $this->baseinfoRepository->getEntryById($baseinfo->id);

        return view('baseinfo.edit', compact(['info']));
    }

    public function update(UpdateBaseinfoRequest $request, Baseinfo $baseinfo)
    {
        $validated = $request->safe()->except(['hero_image']);

        if ($request->has('hero_image')) {

            $this->imageService->deleteHeroImages($baseinfo->hero_image);
            $this->imageService->generateNames($request->file('hero_image'));
            $validated['hero_image'] = $this->imageService->storeHeroImages()->generateHeroURL();
        }

        $this->baseinfoRepository->updateEntry($baseinfo->id, $validated);

        return redirect()->action([BaseinfoController::class, 'edit'], ['baseinfo' => $baseinfo->id]);
    }

    public function destroy(Baseinfo $baseinfo)
    {
        $this->baseinfoRepository->deleteEntry($baseinfo->id);

        return redirect()->action([BaseinfoController::class, 'index']);
    }
}
