<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\User;
use App\Models\Gallery;
use Illuminate\Http\File;
use App\Events\PostCreated;
use App\Events\PostDeleted;
use App\Events\PostUpdated;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Services\CacheService;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdatePostRequest;
use App\Interfaces\PostRepositoryInterface;

class UserPostController extends Controller
{
    // private PostRepositoryInterface $postRepository;

    public function __construct(
      private PostRepositoryInterface $postRepository,
      private ImageService $imageService
    ) {}

    // public function __construct(PostRepositoryInterface $postRepository)
    // {
    //     $this->postRepository = $postRepository;
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user, CacheService $cacheService)
    {
        // TODO - Add policy in order to show posts only for those who created them

        $posts = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() use ($user) {
            return $this->postRepository->getAllEntries($user->id);
        });

        return view('post.index', ['posts' => $posts, 'user' => $user ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
    // TODO - Allow use create form only for Author users - Policies

        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request, User $user)
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

        $post = $this->postRepository->createEntry($user->id, $validated);

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

        PostCreated::dispatch($user, $title, $summary);

        return redirect()->action([UserPostController::class, 'index'], ['user' => $user->id]);

        // TODO - Allow storing only for Author users - later test this feature as well (Policies)
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Post $post)
    {
        // TODO - View increment functionality
        // TODO - Add policy in order to show post only for those who created it

        $post = $this->postRepository->getEntryById($user->id, $post->id);

        return view('post.show', ['post' => $post, 'user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, Post $post)
    {
        // TODO - Allow edit form only for Author users - Policies

        $post = $this->postRepository->getEntryById($user->id, $post->id);

        return view('post.edit', ['post' => $post, 'user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, User $user, Post $post)
    {
        // TODO - add policy in order to update post only for those who created it
        // TODO - email notification (for admin if updated by author, and for author if updated by admin)
        // TODO - published/favorite can be set only by admin user
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

        $this->postRepository->updateEntry($user->id, $post->id, $validated);

        if ($request->has('tags')) {
            $tagIDs = $request->input('tags');
            $post->tags()->sync($tagIDs);
        }

        if ($request->has('categories')) {
            $catsIDs = $request->input('categories');
            $post->categories()->sync($catsIDs);
        }

        PostUpdated::dispatch($user, $title, $summary);

        return redirect()->action([UserPostController::class, 'edit'], ['user' => $user->id, 'post' => $post->slug]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Post $post)
    {
        $title = $post->title;
        $summary = $post->summary ?? $post->title;

        $this->postRepository->deleteEntry($user->id, $post->id);

        PostDeleted::dispatch($user, $title, $summary);

        return redirect()->action([UserPostController::class, 'index'], ['user' => $user->id]);
    }
}
