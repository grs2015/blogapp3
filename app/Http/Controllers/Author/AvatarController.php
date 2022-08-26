<?php

namespace App\Http\Controllers\Author;

use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarRequest;
use App\Actions\Blog\DeleteAvatarImageAction;
use App\Actions\Blog\UpdateAvatarImageAction;

class AvatarController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AvatarRequest $request, ImageService $imageService)
    {
        $user = UpdateAvatarImageAction::execute($request, $imageService);

        return redirect()->action([UserController::class, 'edit'], ['user' => $user->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, ImageService $imageService)
    {
        $user = DeleteAvatarImageAction::execute($request, $imageService);

        return redirect()->action([UserController::class, 'edit'], ['user' => $user->id]);
    }
}
