<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Postmeta;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostmetaRequest;
use App\Http\Requests\UpdatePostmetaRequest;
use App\Interfaces\PostmetaRepositoryInterface;

class PostPostmetaController extends Controller
{
    public function __construct(
        private PostmetaRepositoryInterface $postmetaRepository
    ) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $postmetas = $this->postmetaRepository->getAllEntries($post->id);

        return view('postmeta.index', compact('postmetas', 'post'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('postmeta.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostmetaRequest $request, Post $post)
    {
        $validated = $request->validated();

        $this->postmetaRepository->createEntry($post->id, $validated);

        return redirect()->action([PostPostmetaController::class, 'index'], ['post' => $post->slug]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post, Postmeta $postmeta)
    {
        $postmeta = $this->postmetaRepository->getEntryById($post->id, $postmeta->id);

        return view('postmeta.show', compact('post', 'postmeta'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post, Postmeta $postmeta)
    {
        $postmeta = $this->postmetaRepository->getEntryById($post->id, $postmeta->id);

        return view('postmeta.edit', compact('post', 'postmeta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostmetaRequest $request, Post $post, Postmeta $postmeta)
    {
        $validated = $request->validated();

        $this->postmetaRepository->updateEntry($post->id, $postmeta->id, $validated);

        return redirect()->action([PostPostmetaController::class, 'edit'], ['post' => $post->slug, 'postmeta' => $postmeta->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
