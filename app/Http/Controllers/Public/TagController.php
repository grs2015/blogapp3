<?php

namespace App\Http\Controllers\Public;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\TagRepositoryInterface;

class TagController extends Controller
{
    public function __construct(
        private TagRepositoryInterface $tagRepository
    ) {}

    public function index()
    {
        $tags = $this->tagRepository->getAllEntries();

        return view('public.tag.index', ['tags' => $tags]);
    }

    public function show(Tag $tag)
    {
        $tag = $this->tagRepository->getEntryById($tag->id);

        return view('public.tag.show', ['tag' => $tag]);
    }
}
