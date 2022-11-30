<?php

namespace App\Http\Controllers\InternalUser;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserPermission;

use App\Utilities\SystemConstant;
use App\Http\Controllers\Controller;

class ExtraController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);

        // User permissions setting permission.
        $this->middleware(['user.user.permission.check:UPMP01'])->only(["userPermissionIndex"]);
        $this->middleware(['user.user.permission.check:UPMP02'])->only(["userPermissionDetails"]);
    }

    // User permission setting
    public function userPermissionIndex(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,100);
        $types = array(
                    "All","User module",
                    "Setting module","Business setting module",
                    "Authentication log setting module","Activity log setting module",
                    "User permission setting module"
                );
        $userPermissions = UserPermission::orderBy("id","asc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('type') && in_array($request->type,$types) && !($request->type == "All")){
                $userPermissions = $userPermissions->where("type","like","%".Str::studly($request->type)."%");
            }

            if($request->has('search')){
                if(!($request->search == null)){
                    $userPermissions = $userPermissions->where("name","like","%".$request->search."%")
                                                    ->orWhere("description","like","%".$request->search."%");
                }
            }
        }

        $userPermissions = $userPermissions->paginate($pagination);
        return view('internal user.extra.user permission.index',compact("userPermissions",'paginations','types'));
    }

    public function userPermissionDetails($slug){
        $userPermission = UserPermission::where("slug",$slug)->firstOrFail();
        return view('internal user.extra.user permission.details',compact("userPermission"));
    }
}
