<?php

namespace App\Http\Controllers\Public;

use App\Models\Baseinfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\BaseinfoRepositoryInterface;

class BaseinfoController extends Controller
{
    public function __construct(
        private BaseinfoRepositoryInterface $baseinfoRepository,
    ) {  }

    public function index()
    {
        $infos = $this->baseinfoRepository->getAllEntries();

        return view('public.info.index', compact(['infos']));
    }
}
