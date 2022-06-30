<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBaseinfoRequest;
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
            $filename = "{$timestamp}-{$file->getClientOriginalName()}";

            $path = Storage::putFileAs('uploads', $file, $filename);
            $validated['hero_image'] = $path;
        }

        $this->baseinfoRepository->createEntry($validated);

        return redirect()->action([BaseinfoController::class, 'index']);
    }
}
