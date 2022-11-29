<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(['user.user.permission.check:UMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:UMP02,UMP03'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:UMP04'])->only(["details"]);
        $this->middleware(['user.user.permission.check:UMP05,UMP06'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:UMP07,UMP08'])->only(["trash"]);
        $this->middleware(['user.user.permission.check:UMP09,UMP10'])->only(["restore"]);
    }

    public function index(Request $request)
    {
        $pagination = 1;

        $paginations = array(1,5,15,30,45,60,75,90,100);
        $searchFields = array("All","Name","Email","Mobile no");

        $activeUsers = User::orderby("name","desc");
        $trashUsers = User::onlyTrashed()->orderby("name","asc");

        if(count($request->input()) > 0){

            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('user_role') && !($request->user_role == null) && (!($request->user_role == "All")) && (in_array($request->user_role,array("Owner","Subordinate")))){
                switch ($request->selected_nav_tab) {
                    case "Active":
                        $activeUsers = $activeUsers->where("user_role",$request->user_role);
                    break;

                    case "Trash":
                        $trashUsers = $trashUsers->where("user_role",$request->user_role);
                    break;

                    default:
                        abort(404,"Unknown requested nav.");
                    break;
                }
            }

            if(($request->has('search_field') && ($request->has('search') && !($request->search_field == null)) && !($request->search == null))){

                $searchCustomerField = (in_array( $request->search_field,$searchFields)) ? Str::lower(str_replace(" ","_",$request->search_field) ): null;

                if(!($searchCustomerField == null)){
                    switch ($request->selected_nav_tab) {
                        case "Active":
                            $activeUsers = $activeUsers->whereNotNull("id");

                            switch ($searchCustomerField) {
                                case "all":
                                    $activeUsers = $activeUsers->orWhere("name","like","%".$request->search."%")
                                                                    ->orWhere("email","like","%".$request->search."%")
                                                                    ->orWhere("mobile_no","like","%".$request->search."%");
                                break;

                                default:
                                    $activeUsers = $activeUsers->where($searchCustomerField,"like","%".$request->search."%");
                                break;
                            }
                        break;

                        case "Trash":
                            $trashUsers = $trashUsers->whereNotNull("id");

                            switch ($searchCustomerField) {
                                case "all":
                                    $trashUsers = $trashUsers->orWhere("name","like","%".$request->search."%")
                                                                    ->orWhere("email","like","%".$request->search."%")
                                                                    ->orWhere("mobile_no","like","%".$request->search."%");
                                break;

                                default:
                                    $trashUsers = $trashUsers->where($searchCustomerField,"like","%".$request->search."%");
                                break;
                            }
                        break;

                        default:
                            abort(404,"Unknown requested nav.");
                        break;
                    }
                }
            }
        }

        $trashUsers = $trashUsers->paginate($pagination);
        $activeUsers = $activeUsers->paginate($pagination);

        return view('internal user.user.index',compact("activeUsers","trashUsers","paginations","searchFields"));
    }

    public function details($slug){
        $user = User::withTrashed()->where("slug",$slug)->firstOrFail();
        return view('internal user.user.details',compact("user"));
    }
}
