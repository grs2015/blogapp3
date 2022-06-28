<?php

namespace App\Http\Controllers;

use App\Interfaces\TagRepositoryInterface;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(
        private TagRepositoryInterface $tagRepositoryInterface
    ) {}


    public function index()
    {
        $tags = $this->tagRepositoryInterface->getAllEntries();

        return view('tag.index', ['tags' => $tags]);
    }

    public function create()
    {
        return view('tag.create');
    }

    public function store(Request $request)
    {

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
