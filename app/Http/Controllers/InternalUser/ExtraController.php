<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserPermission;
use App\Utilities\SystemConstant;
use App\Models\UserPermissionGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;

class ExtraController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);

        // User permissions.
        $this->middleware(['user.user.permission.check:UPMP01'])->only(["userPermissionIndex"]);
        $this->middleware(['user.user.permission.check:UPMP02'])->only(["userPermissionDetails"]);

        // User permission group.
        $this->middleware(['user.user.permission.check:UPGMP01'])->only(["userPermissionGroupIndex"]);
        $this->middleware(['user.user.permission.check:UPGMP02'])->only(["userPermissionGroupCreate","userPermissionGroupSave"]);
        $this->middleware(['user.user.permission.check:UPGMP03'])->only(["userPermissionDetails"]);
        $this->middleware(['user.user.permission.check:UPGMP04'])->only(["userPermissionGroupEdit","userPermissionGroupUpdate"]);
        $this->middleware(['user.user.permission.check:UPGMP05'])->only(["userPermissionDelete"]);
    }

    // User permission
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

    // User permission group
    public function userPermissionGroupIndex(Request $request){
        $pagination = 1;
        $paginations = array(5,15,30,45,60,75,90,100);
        $userPermissions = UserPermission::orderBy("id","asc")->get();

        $userPermissionGroups = UserPermissionGroup::orderBy("name","asc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('user_permission') && !($request->user_permission == null) && !($request->user_permission == "All")){
                $userPermission = UserPermission::where("slug",$request->user_permission)->first();
                if($userPermission){
                    $userPermissionGroups = $userPermissionGroups->whereIn("id",$userPermission->userPermissionGroups->pluck("id"));
                }
            }

            if($request->has('search')){
                if(!($request->search == null)){
                    $userPermissionGroups = $userPermissionGroups->where("name","like","%".$request->search."%")
                                                    ->orWhere("code","like","%".$request->search."%")
                                                    ->orWhere("description","like","%".$request->search."%");
                }
            }
        }

        $userPermissionGroups = $userPermissionGroups->paginate($pagination);
        return view('internal user.extra.user permission group.index',compact("userPermissionGroups",'paginations','userPermissions'));
    }

    public function userPermissionGroupCreate(){
        $userPermissions = UserPermission::orderBy("type")->orderBy("name")->get()->groupBy("type");
        return view('internal user.extra.user permission group.create',compact('userPermissions'));
    }

    public function userPermissionGroupSave(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:100',
                'code' => 'required|max:100|unique:user_permission_groups,code',
                'user_permission' => 'required'
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 100 chars.',

                'code.required' => 'Code is required.',
                'code.max' => 'Code length can not greater then 100 chars.',
                'code.unique' => 'Code must be unique.',

                'user_permission.required' => 'User permission is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();

            $validUserPermission = 0;
            foreach($afterValidatorData["user_permission"] as $perUserPermission){
                if(UserPermission::where("slug",$perUserPermission)->count() > 0){
                    $validUserPermission = $validUserPermission + 1;
                }
            }

            if(count($afterValidatorData["user_permission"]) == 0){
                $validator->errors()->add(
                    'user_permission', "User permission is required."
                );
            }
            else{
                if(!(count($afterValidatorData["user_permission"]) == $validUserPermission)){
                    $validator->errors()->add(
                        'user_permission', "Some unknown user permission is found."
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userPermissionIds = array();
        $statusInformation = array("status" => "errors","message" => array());

        LogBatch::startBatch();
            $userPermissionGroup = new UserPermissionGroup();
            $userPermissionGroup->name = $request->name;
            $userPermissionGroup->code = $request->code;
            $userPermissionGroup->slug = SystemConstant::slugGenerator($request->name,200);
            $userPermissionGroup->created_at = Carbon::now();
            $userPermissionGroup->created_by_id = Auth::user()->id;
            $userPermissionGroup->updated_at = null;
            $saveUserPermissionGroup = $userPermissionGroup->save();

            if($saveUserPermissionGroup){
                foreach($request->user_permission as $perUserPermission){
                    $userPermission = UserPermission::where("slug",$perUserPermission)->firstOrFail();
                    array_push($userPermissionIds,$userPermission->id);
                }

                $userPermissionIds = SystemConstant::arraySort($userPermissionIds,"Value","Asc");

                if(count($userPermissionIds) > 0){
                    $userPermissionGroup->userPermissions()->attach(
                        $userPermissionIds,["created_at" => Carbon::now(),"updated_at" => null,"created_by_id" => Auth::user()->id]
                    );
                }

            }
        LogBatch::endBatch();

        if($saveUserPermissionGroup){
            $statusInformation["status"] = "status";
            array_push($statusInformation["message"],"User permission group successfully created.");

            if(count($userPermissionIds) == count($request->user_permission)){
                array_push($statusInformation["message"],"All selected user permission are added to uer permission group.");
            }
            else{
                $statusInformation["status"] = "warning";
                array_push($statusInformation["message"],"Some selected user permission are fail to add to uer permission group.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to create user permission group.";
        }

        return redirect()->route("user.permission.group.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }
}
