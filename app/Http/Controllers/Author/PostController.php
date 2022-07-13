<?php

namespace App\Http\Controllers\Author;

use App\Models\Post;
use App\Events\PostCreated;
use App\Events\PostDeleted;
use App\Events\PostUpdated;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Interfaces\PostRepositoryInterface;

class PostController extends Controller
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private ImageService $imageService
    )
    {
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CacheService $cacheService)
    {
        // $posts = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() {
        //     return $this->postRepository->getAllEntries(auth()->user()->id);
        // });

        $posts = Post::where('author_id', auth()->user()->id)->with(['comments', 'postmetas'])->get();

        return view('author.post.index', ['posts' => $posts, 'user' => auth()->user()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('author.post.create');
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
        $validated['published'] = Post::UNPUBLISHED;
        $validated['favorite'] = Post::NONFAVORITE;
        $title = $validated['title'];
        $summary = $validated['summary'] ?? $validated['title'];

        if ($request->has('hero_image')) {

            $this->imageService->generateNames($request->file('hero_image'));
            $validated['hero_image'] = $this->imageService->storeThumbHeroImages([[100, 100], [200, 200], [640, 480]])
                ->storeHeroImages()->generateHeroURL()->filenamesDB;
        }

        $post = $this->postRepository->createEntry(auth()->user()->id, $validated);

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

        PostCreated::dispatch(auth()->user(), $title, $summary);

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

        return view('author.post.show', ['post' => $post, 'user' => $post->user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $post = Post::where('author_id', $post->author_id)->where('id', $post->id)->firstOrFail();
        // $post = $this->postRepository->getEntryById($post->author_id, $post->id);

        return view('author.post.edit', ['post' => $post, 'user' => $post->user]);
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
        // TODO - email notification (for admin if updated by author, and for author if updated by admin)
        // TODO - add necessary exceptions/test for them

        $validated = $request->safe()->except(['published', 'views', 'favorite', 'tags', 'categories', 'hero_image', 'images']);
        $title = $validated['title'];
        $summary = $validated['summary'] ?? $validated['title'];

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

        $this->postRepository->updateEntry(auth()->user()->id, $post->id, $validated);

        if ($request->has('tags')) {
            $tagIDs = $request->input('tags');
            $post->tags()->sync($tagIDs);
        }

        if ($request->has('categories')) {
            $catsIDs = $request->input('categories');
            $post->categories()->sync($catsIDs);
        }

        PostUpdated::dispatch(auth()->user(), $title, $summary);

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
        $title = $post->title;
        $summary = $post->summary ?? $post->title;

        $this->postRepository->deleteEntry(auth()->user()->id, $post->id);

        PostDeleted::dispatch(auth()->user(), $title, $summary);

        return redirect()->action([PostController::class, 'index']);
    }
}
