<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Category;
use App\Filters\PostFilter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\ImageService;
use App\DataTransferObjects\TagData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTransferObjects\PostData;
use App\DataTransferObjects\UserData;
use App\ViewModels\GetPostsViewModel;
use Illuminate\Http\RedirectResponse;
use App\Actions\Blog\UpsertPostAction;
use App\Http\Requests\PostTrashRequest;
use App\Http\Requests\StorePostRequest;
use App\ViewModels\UpsertPostViewModel;
use App\Http\Requests\UpdatePostRequest;
use App\DataTransferObjects\CategoryData;
use App\ViewModels\GetSinglePostViewModel;
use App\Actions\Blog\ForceDeletePostAction;
use App\DataTransferObjects\ForceDeletePostData;

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
        return Inertia::render('Post/Index', [
            'model' => new GetPostsViewModel($cacheService, $request, $filters)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Post/Form', [
            'model' => new UpsertPostViewModel()
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
        return Inertia::render('Post/Form', [
            'model' => new UpsertPostViewModel($post)
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
        return Inertia::render('Post/Show', [
            'model' => new GetSinglePostViewModel($post)
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
        $post->delete();

        return redirect()->action([PostController::class, 'index']);
    }

    public function delete(ForceDeletePostData $data, ImageService $imageService)
    {
        ForceDeletePostAction::execute($data, $imageService);

        return redirect()->action([PostController::class, 'index']);
    }

    public function restore(PostTrashRequest $request)
    {
        Post::restoreTrashed($request->ids);

        return redirect()->action([PostController::class, 'index']);
    }
}
