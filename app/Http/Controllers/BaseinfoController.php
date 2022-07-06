<?php

namespace App\Http\Controllers;

use App\Models\Baseinfo;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBaseinfoRequest;
use App\Http\Requests\UpdateBaseinfoRequest;
use App\Interfaces\BaseinfoRepositoryInterface;

class BaseinfoController extends Controller
{
    public function __construct(
        private BaseinfoRepositoryInterface $baseinfoRepository
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
            $file = $request->file('hero_image');

            $timestamp = now()->format('Y-m-d-H-i-s');
            $filenameWithExtension = "{$timestamp}-{$file->getClientOriginalName()}";
            $filename = pathinfo($filenameWithExtension)['filename'];
            $filenames = collect([]);

            $fileHiRes = Image::make($file);
            $fileLoRes = $fileHiRes->fit(null, 600, function($constraint) { $constraint->upsize(); });
            Storage::put("uploads/HiRes-{$filename}.{$file->getClientOriginalExtension()}", $fileHiRes->stream('jpg', 100));
            Storage::put("uploads/LoRes-{$filename}.{$file->getClientOriginalExtension()}", $fileLoRes->stream('jpg', 60));
            $urlLoRes = str_replace('/storage/', '', Storage::url("uploads/LoRes-{$filename}.{$file->getClientOriginalExtension()}"));
            $urlHiRes = str_replace('/storage/', '', Storage::url("uploads/HiRes-{$filename}.{$file->getClientOriginalExtension()}"));
            $filenames->push($urlHiRes, $urlLoRes);
            $filenamesDB = $filenames->implode(',');
            $validated['hero_image'] = $filenamesDB;
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
            try {
                $namesArr = explode(',', $baseinfo->hero_image);
                Storage::disk('public')->delete($namesArr);
            } catch(\Exception $e) {
                throw $e;
            }

            $file = $request->file('hero_image');

            $timestamp = now()->format('Y-m-d-H-i-s');
            $filenameWithExtension = "{$timestamp}-{$file->getClientOriginalName()}";
            $filename = pathinfo($filenameWithExtension)['filename'];
            $filenames = collect([]);

            $fileHiRes = Image::make($file);
            $fileLoRes = $fileHiRes->fit(null, 600, function($constraint) { $constraint->upsize(); });
            Storage::put("uploads/HiRes-{$filename}.{$file->getClientOriginalExtension()}", $fileHiRes->stream('jpg', 100));
            Storage::put("uploads/LoRes-{$filename}.{$file->getClientOriginalExtension()}", $fileLoRes->stream('jpg', 60));
            $urlLoRes = str_replace('/storage/', '', Storage::url("uploads/LoRes-{$filename}.{$file->getClientOriginalExtension()}"));
            $urlHiRes = str_replace('/storage/', '', Storage::url("uploads/HiRes-{$filename}.{$file->getClientOriginalExtension()}"));
            $filenames->push($urlHiRes, $urlLoRes);
            $filenamesDB = $filenames->implode(',');
            $validated['hero_image'] = $filenamesDB;
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
