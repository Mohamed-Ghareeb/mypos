<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function GuzzleHttp\json_decode;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    } // end of index
}
