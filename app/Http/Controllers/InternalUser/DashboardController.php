<?php

namespace App\Http\Controllers\InternalUser;

use Illuminate\Http\Request;
use App\Models\ProjectContract;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $projectContracts = ProjectContract::orderBy("created_at","desc")->orderBy("name","asc")->get();
        return view('internal user.dashboard.index',compact("projectContracts"));
    }
}
