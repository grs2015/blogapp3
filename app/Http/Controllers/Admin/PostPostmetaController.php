<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Inertia\Inertia;
use App\Models\Postmeta;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\PostmetaData;

use App\ViewModels\GetPostmetasViewModel;
use App\Actions\Blog\UpsertPostmetaAction;
use App\Http\Requests\StorePostmetaRequest;
use App\ViewModels\UpsertPostmetaViewModel;
use App\Http\Requests\UpdatePostmetaRequest;
use App\Interfaces\PostmetaRepositoryInterface;

class PostPostmetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        return Inertia::render('Postmeta/Index', [
            'model' => new GetPostmetasViewModel($post)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Postmeta/Form', [
            'model' => new UpsertPostmetaViewModel()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post, Postmeta $postmeta)
    {
        return Inertia::render('Postmeta/Form', [
            'model' => new UpsertPostmetaViewModel($postmeta)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post, Postmeta $postmeta)
    {
        return Inertia::render('Postmeta/Show', [
            'model' => new UpsertPostmetaViewModel($postmeta)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostmetaData $data, Post $post)
    {
        UpsertPostmetaAction::execute($data);

        return redirect()->action([PostPostmetaController::class, 'index'], ['post' => $post->slug]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostmetaData $data, Post $post)
    {
        UpsertPostmetaAction::execute($data);

        return redirect()->action([PostPostmetaController::class, 'edit'], ['post' => $post->slug, 'postmeta' => $data->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, Postmeta $postmeta)
    {
        $postmeta->delete();

        return redirect()->action([PostPostmetaController::class, 'index'], ['post' => $post->slug]);
    }
}
