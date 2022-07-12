<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PostTrashRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Interfaces\PostRepositoryInterface;

class PostController extends Controller
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private ImageService $imageService
      ) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(['user', 'comments', 'postmetas'])->get();

        return view('admin.post.index', compact(['posts']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $validated = $request->safe()->except(['tags', 'categories', 'hero_image', 'images']);
        $validated['views'] = 0;

        if ($request->has('hero_image')) {

            $this->imageService->generateNames($request->file('hero_image'));
            $validated['hero_image'] = $this->imageService->storeThumbHeroImages([[100, 100], [200, 200], [640, 480]])
                ->storeHeroImages()->generateHeroURL()->filenamesDB;
        }

        $post = $this->postRepository->createEntry(Auth::user()->id, $validated);

        if ($request->has('images')) {

            $this->imageService->generateGallery($request->allFiles('images'), $post, [[200, 200], [640, 480]]);
        }

        if ($request->has('tags')) {
            $tagIDs = $request->input('tags');
            $post->tags()->sync($tagIDs);
        }

        if ($request->has('categories')) {
            $catsIDs = $request->input('categories');
            $post->categories()->sync($catsIDs);
        }

        return redirect()->action([PostController::class, 'index']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $post = Post::where('author_id', $post->author_id)->where('id', $post->id)->firstOrFail();
        // $post = $this->postRepository->getEntryById($post->author_id, $post->id);

        return view('admin.post.show', ['post' => $post, 'user' => $post->user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $post = Post::where('author_id', $post->author_id)->firstWhere('id', $post->id);

        return view('admin.post.edit', ['post' => $post, 'user' => $post->user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->safe()->except(['views', 'tags', 'categories', 'hero_image', 'images']);

        if ($request->has('title')) {
            $validated['slug'] = Str::slug($validated['title'], '-');
        }

        if ($request->has('hero_image')) {

            $this->imageService->deleteHeroImages($post->hero_image);
            $this->imageService->generateNames($request->file('hero_image'));
            $validated['hero_image'] = $this->imageService->storeThumbHeroImages([[100, 100], [200, 200], [640, 480]])
                ->storeHeroImages()->generateHeroURL()->filenamesDB;
        }

        if ($request->has('images')) {

            $this->imageService->deleteGallery($post);
            $this->imageService->generateGallery($request->allFiles('images'), $post, [[200, 200], [640, 480]]);
        }

        $this->postRepository->updateEntry($post->author_id, $post->id, $validated);

        if ($request->has('tags')) {
            $tagIDs = $request->input('tags');
            $post->tags()->sync($tagIDs);
        }

        if ($request->has('categories')) {
            $catsIDs = $request->input('categories');
            $post->categories()->sync($catsIDs);
        }

        return redirect()->action([PostController::class, 'edit'], ['post' => $post->slug]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $this->postRepository->deleteEntry($post->author_id, $post->id);

        return redirect()->action([PostController::class, 'index']);
    }

    public function delete(PostTrashRequest $request): RedirectResponse
    {
        $postToDelete = $this->postRepository->forceDeleteEntry($request->ids);
        $postToDelete->each(function($item) {
            $item->tags()->detach();
            $item->categories()->detach();
            $item->forceDelete();
        });

        return redirect()->action([PostController::class, 'index']);
    }

    public function restore(PostTrashRequest $request): RedirectResponse
    {
        $this->postRepository->restoreEntry($request->ids);

        return redirect()->action([PostController::class, 'index']);
    }


}
