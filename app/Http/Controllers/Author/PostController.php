<?php

namespace App\Http\Controllers\Author;

use App\Models\Post;
use Inertia\Inertia;
use App\Events\PostCreated;
use App\Events\PostDeleted;
use App\Events\PostUpdated;
use App\Filters\PostFilter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTransferObjects\PostData;
use App\ViewModels\GetPostsViewModel;
use Illuminate\Support\Facades\Cache;
use App\Actions\Blog\DeletePostAction;
use App\Actions\Blog\UpsertPostAction;
use App\Http\Requests\StorePostRequest;
use App\ViewModels\UpsertPostViewModel;
use App\Http\Requests\UpdatePostRequest;
use App\ViewModels\GetSinglePostViewModel;
use App\Interfaces\PostRepositoryInterface;

class PostController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, CacheService $cacheService, PostFilter $filters)
    {
        $this->authorize('viewAny', Post::class);

        return Inertia::render('Post/Author/Index', [
            'model' => new GetPostsViewModel($cacheService, $request, $filters)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Post::class);

        return Inertia::render('Post/Author/Form', [
            'model' => new UpsertPostViewModel()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return Inertia::render('Post/Author/Show', [
            'model' => new GetSinglePostViewModel($post)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return Inertia::render('Post/Author/Form', [
            'model' => new UpsertPostViewModel($post)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostData $data, Request $request, ImageService $imageService)
    {
        $this->authorize('create', Post::class);

        UpsertPostAction::execute($data, $imageService, collect($request->allFiles()));

        return redirect()->action([PostController::class, 'index']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostData $data, Request $request, ImageService $imageService)
    {
        $this->authorize('update', Post::getEntityById($data->id));

        UpsertPostAction::execute($data, $imageService, collect($request->allFiles()));

        return redirect()->action([PostController::class, 'edit'], ['post' => $data->slug]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        DeletePostAction::execute($post);

        return redirect()->action([PostController::class, 'index']);
    }
}
