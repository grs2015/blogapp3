<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use Inertia\Inertia;
use App\Events\TagCreated;
use App\Events\TagDeleted;
use App\Events\TagUpdated;
use App\Filters\TagFilter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\DataTransferObjects\TagData;
use App\Http\Controllers\Controller;
use App\ViewModels\GetTagsViewModel;
use App\Actions\Blog\DeleteTagAction;
use App\Actions\Blog\UpsertTagAction;
use App\Http\Requests\StoreTagRequest;
use App\ViewModels\UpsertTagViewModel;
use App\Http\Requests\UpdateTagRequest;
use App\Interfaces\TagRepositoryInterface;


class TagController extends Controller
{
    public function index(Request $request, TagFilter $filters)
    {
        return Inertia::render('Tag/Index', [
            'model' => new GetTagsViewModel($request, $filters)
        ]);
    }

    public function create()
    {
        return Inertia::render('Tag/Form', [
           'model' => new UpsertTagViewModel()
        ]);
    }

    public function edit(Tag $tag)
    {
        return Inertia::render('Tag/Form', [
            'model' => new UpsertTagViewModel($tag)
        ]);
    }

    public function show(Tag $tag)
    {
        return Inertia::render('Tag/Show', [
            'model' => new UpsertTagViewModel($tag)
        ]);
    }

    public function store(TagData $data)
    {
        UpsertTagAction::execute($data);

        return redirect()->action([TagController::class, 'index']);
    }

    public function update(TagData $data)
    {
        UpsertTagAction::execute($data);

        return redirect()->action([TagController::class, 'edit'], ['tag' => $data->slug]);
    }

    public function destroy(Tag $tag)
    {
        DeleteTagAction::execute($tag);

        return redirect()->action([TagController::class, 'index']);
    }
}
