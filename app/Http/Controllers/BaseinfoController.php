<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\BaseinfoRepositoryInterface;

class BaseinfoController extends Controller
{
    public function __construct(
        private BaseinfoRepositoryInterface $baseinfoRepository
    ) {  }

    public function index()
    {
        $infos = $this->baseinfoRepository->getAllEntries();

        return view('baseinfo.index', compact(['infos']));
    }

    public function create()
    {
        return view('baseinfo.create');
    }
}
