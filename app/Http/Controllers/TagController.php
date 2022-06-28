<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTagRequest;
use App\Interfaces\TagRepositoryInterface;

class TagController extends Controller
{
    public function __construct(
        private TagRepositoryInterface $tagRepository
    ) {}


    public function index()
    {
        $tags = $this->tagRepository->getAllEntries();

        return view('tag.index', ['tags' => $tags]);
    }

    public function create()
    {
        return view('tag.create');
    }

    public function store(StoreTagRequest $request)
    {
        $validated = $request->validate();

        $this->tagRepository->createEntry($validated);

        return redirect()->action([TagController::class, 'index']);
    }

    public function show(Tag $tag)
    {

    }

    public function edit(Tag $tag)
    {

    }

    public function update(Request $reques, Tag $tag)
    {

    }

    public function destroy(Tag $tag)
    {

    }
}
