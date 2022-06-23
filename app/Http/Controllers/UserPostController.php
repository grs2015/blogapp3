<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\PostRepositoryInterface;

class UserPostController extends Controller
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
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

        if ($request->has('hero_image')) {
            $file = $request->file('hero_image');
            $timestamp = now()->format('Y-m-d-H-i-s');
            $filename = "{$timestamp}-{$file->getClientOriginalName()}";

            Storage::putFileAs('uploads', $file, $filename);
            $url = parse_url(Storage::url("uploads/{$filename}"), PHP_URL_PATH);
        }

        $post = $this->postRepository->createEntry($user->id, $validated);

        return redirect()->action([UserPostController::class, 'index'], ['user' => $user->id]);

        // TODO - Allow storing only for Author users - later test this feature as well

        // TODO - Store functionality for hero-image
        // TODO - Store functionality for post-images

        // TODO - If category/tags IDs received with request, connect them as sync (M-2-M) to created post
        // TODO - after successful creation send the notification to admin user
        // TODO - Stored post must have statuses: unpublished and favotire: usual with views: zero
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
        // TODO - Corresponding View class
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, Post $post)
    {
        // TODO - Corresponding View class
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user, Post $post)
    {
        // TODO - in test add the check of previous file deletion after update
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
