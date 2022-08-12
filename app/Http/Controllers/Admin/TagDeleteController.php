<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\Blog\MassDeleteTagAction;
use App\Http\Requests\TagMassDeleteRequest;
use App\Http\Controllers\Admin\TagController;
use App\Actions\Blog\MassDeleteCategoryAction;

class TagDeleteController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(TagMassDeleteRequest $request)
    {
        MassDeleteTagAction::execute($request->data);

        return redirect()->action([TagController::class, 'index']);
    }
}
