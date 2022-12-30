<?php

namespace App\Http\Controllers\InternalUser;


use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Http\Controllers\Controller;


class OilAndGasPumpController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:OAGPMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:OAGPMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:OAGPMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:OAGPMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:OAGPMP05'])->only(["delete"]);
    }

    public function index(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);

        $oilAndGasPumps = OilAndGasPump::orderby("created_at","desc")->orderby("name","asc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('search') && !($request->search == null)){
                $oilAndGasPumps = $oilAndGasPumps->where("name","like","%".$request->search."%")
                                                ->orWhere("description","like","%".$request->search."%")
                                                ->orWhere("code","like","%".$request->search."%");
            }
        }

        $oilAndGasPumps = $oilAndGasPumps->paginate($pagination);

        return view('internal user.oil and gas pump.oil and gas pump.index',compact("oilAndGasPumps","paginations"));
    }

}
