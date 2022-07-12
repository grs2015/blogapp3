<?php
namespace App\Http\Controllers\Author;

use App\Models\Tag;
use App\Events\TagCreated;
use App\Events\TagDeleted;
use App\Events\TagUpdated;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Interfaces\TagRepositoryInterface;

class TagController extends Controller
{
    public function __construct(
        private TagRepositoryInterface $tagRepository
    ) {}


    public function index()
    {
        $tags = $this->tagRepository->getAllEntries();

        return view('author.tag.index', ['tags' => $tags]);
    }

    public function show(Tag $tag)
    {
        $tag = $this->tagRepository->getEntryById($tag->id);

        return view('author.tag.show', ['tag' => $tag]);
    }
}
