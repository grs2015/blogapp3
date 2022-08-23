<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;

class StatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StatusRequest $request)
    {
        $user = User::getEntityById($request->id);
        $request->status === 'enabled' ? $user->markAsEnabled() : $user->markAsDisabled();

        return redirect()->action([UserController::class, 'index'], ['page' => $request->page, 'per_page' => $request->per_page]);
    }
}
