<?php

namespace App\Http\Controllers;

use App\Events\TagCreated;
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
        $validated = $request->validated();
        $title = $validated['title'];
        $content = $validated['content'] ?? null;

        $this->tagRepository->createEntry($validated);

        TagCreated::dispatch($title, $content);

        return redirect()->action([TagController::class, 'index']);
    }

    public function show(Tag $tag)
    {
        $tag = $this->tagRepository->getEntryById($tag->id);

        return view('tag.show', ['tag' => $tag]);
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
