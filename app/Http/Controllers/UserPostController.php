<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Events\PostCreated;
use App\Events\PostUpdated;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdatePostRequest;
use App\Interfaces\PostRepositoryInterface;

class UserPostController extends Controller
{
    // private PostRepositoryInterface $postRepository;

    public function __construct(
      private PostRepositoryInterface $postRepository
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
    public function index(User $user)
    {
        // TODO - Add policy in order to show posts only for those who created them

        $posts = $this->postRepository->getAllEntries($user->id);

        return view('post.index', ['posts' => $posts, 'user' => $user ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
            $file = $request->file('hero_image');
            $timestamp = now()->format('Y-m-d-H-i-s');
            $filename = "{$timestamp}-{$file->getClientOriginalName()}";

            $path = Storage::putFileAs('uploads', $file, $filename);
            // $url = parse_url(Storage::url("uploads/{$filename}"), PHP_URL_PATH);
            $validated['hero_image'] = $path;
        }

        if ($request->has('images')) {
            $files = $request->allFiles('images');
            $fileNames = collect([]);
            collect($files['images'])->each(function($file) use ($fileNames) {
                $timestamp = now()->format('Y-m-d-H-i-s');
                $filename = "{$timestamp}-{$file->getClientOriginalName()}";
                $path = Storage::putFileAs('uploads', $file, $filename);
                // $fileNames->push(parse_url(Storage::url("uploads/{$filename}"), PHP_URL_PATH));
                $fileNames->push($path);
            });
            $fileNamesDB = $fileNames->implode(',');
            $validated['images'] = $fileNamesDB;
        }

        $post = $this->postRepository->createEntry($user->id, $validated);

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
        // TODO - Add library for image processing
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
        // TODO - in test add the check of previous file(s) deletion after update
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
            $file = $request->file('hero_image');
            $timestamp = now()->format('Y-m-d-H-i-s');
            $filename = "{$timestamp}-{$file->getClientOriginalName()}";

            try {
                Storage::disk('public')->delete($post->hero_image);
            } catch(\Exception $e) {
                throw $e;
            }

            $path = Storage::putFileAs('uploads', $file, $filename);
            // $url = parse_url(Storage::url("uploads/{$filename}"), PHP_URL_PATH);
            $validated['hero_image'] = $path;
        }

        if ($request->has('images')) {
            try {
                $namesArr = explode(',', $post->images);
                Storage::disk('public')->delete($namesArr);
            } catch(\Exception $e) {
                throw $e;
            }

            $files = $request->allFiles('images');
            $fileNames = collect([]);
            collect($files['images'])->each(function($file) use ($fileNames) {
                $timestamp = now()->format('Y-m-d-H-i-s');
                $filename = "{$timestamp}-{$file->getClientOriginalName()}";
                $path = Storage::putFileAs('uploads', $file, $filename);
                // $fileNames->push(parse_url(Storage::url("uploads/{$filename}"), PHP_URL_PATH));
                $fileNames->push($path);
            });
            $fileNamesDB = $fileNames->implode(',');
            $validated['images'] = $fileNamesDB;
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
        //
    }
}
