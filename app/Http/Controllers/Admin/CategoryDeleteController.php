<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\Blog\MassDeleteCategoryAction;
use App\Http\Requests\CategoryMassDeleteRequest;
use App\Http\Controllers\Admin\CategoryController;

class CategoryDeleteController extends Controller
{
    public function mass_delete(CategoryMassDeleteRequest $request)
    {
        MassDeleteCategoryAction::execute($request->data);

        return redirect()->action([CategoryController::class, 'index']);
    }
}
