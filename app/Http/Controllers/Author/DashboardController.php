<?php

namespace App\Http\Controllers\Author;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ViewModels\GetAuthorDashboardViewModel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Dashboard/Author', [
            'model' => new GetAuthorDashboardViewModel($request)
        ]);
    }
}
