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
        $pagination = 5;
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

    public function userPermissionGroupEdit($slug){
        $userPermissions = UserPermission::orderBy("type")->orderBy("name")->get()->groupBy("type");

        $userPermissionGroup = UserPermissionGroup::where("slug",$slug)->firstorFail();
        return view('internal user.extra.user permission group.edit',compact('userPermissions',"userPermissionGroup"));
    }

    public function userPermissionGroupDetails($slug){
        $userPermissionGroup = UserPermissionGroup::where("slug",$slug)->firstorFail();
        return view('internal user.extra.user permission group.details',compact("userPermissionGroup"));
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
        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $userPermissionGroup = new UserPermissionGroup();
            $userPermissionGroup->name = $request->name;
            $userPermissionGroup->code = $request->code;
            $userPermissionGroup->slug = SystemConstant::slugGenerator($request->name,200);
            $userPermissionGroup->created_at = Carbon::now();
            $userPermissionGroup->created_by_id = Auth::user()->id;
            $userPermissionGroup->updated_at = null;
            $saveUserPermissionGroup = $userPermissionGroup->save();
        LogBatch::endBatch();

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

            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully created.");

            if($userPermissionGroup->userPermissions()->count() == count($request->user_permission)){
                $statusInformation["message"]->push("Selected user permissions are sync successfully.");
            }
            else{
                $statusInformation["status"] = "warning";
                $statusInformation["message"]->push("Some selected user permissions are fail to sync.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to create.";
        }

        return redirect()->route("user.permission.group.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function userPermissionGroupUpdate(Request $request,$slug){
        $userPermissionGroupId = UserPermissionGroup::where("slug",$slug)->firstOrFail()->id;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:100',
                'code' => 'required|max:100|unique:user_permission_groups,code,'.$userPermissionGroupId,
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
        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $userPermissionGroup = UserPermissionGroup::where("slug",$slug)->firstorFail();
            $userPermissionGroup->name = $request->name;
            $userPermissionGroup->code = $request->code;
            $userPermissionGroup->slug = SystemConstant::slugGenerator($request->name,200);
            $userPermissionGroup->updated_at = Carbon::now();
            $updateUserPermissionGroup = $userPermissionGroup->update();

        LogBatch::endBatch();

        if($updateUserPermissionGroup){

            if($updateUserPermissionGroup){
                foreach($request->user_permission as $perUserPermission){
                    $userPermission = UserPermission::where("slug",$perUserPermission)->firstOrFail();
                    array_push($userPermissionIds,$userPermission->id);
                }


                $userPermissionIds = SystemConstant::arraySort($userPermissionIds,"Value","Asc");

                if(count($userPermissionIds) > 0){
                    $syncuserPermissionData = array();
                    foreach($userPermissionIds as $perUPId){
                        $syncuserPermissionData[$perUPId] = ["created_at" => Carbon::now(),"updated_at" => Carbon::now(),"created_by_id" => Auth::user()->id];
                    }

                    $userPermissionGroup->userPermissions()->sync($syncuserPermissionData);
                }

            }

            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully updated.");

            if($userPermissionGroup->userPermissions()->count() == count($request->user_permission)){
                $statusInformation["message"]->push("Selected user permissions are successfully snyc.");
            }
            else{
                $statusInformation["status"] = "warning";
                $statusInformation["message"]->push("Some selected user permissions are fail to sync.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to update.";
        }

        return redirect()->route("user.permission.group.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function userPermissionGroupDelete($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        $passedDeleteValidation = $this->userPermissionGroupDeleteValidation($slug);

        if( $passedDeleteValidation["status"] == "status"){
            $userPermissionGroup = UserPermissionGroup::where("slug",$slug)->firstorFail();
            $deleteUserPermissionGroup =  $userPermissionGroup->delete();

            if($deleteUserPermissionGroup){
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully deleted.");
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to delete.");
            }
        }
        else{
            $statusInformation["status"] = $passedDeleteValidation["status"];

            $statusInformation["message"]->push("Can not delete record.");
            $statusInformation["message"]->push("Please remove below dependency first.");

            foreach($passedDeleteValidation["message"] as $errorMessage){
                $statusInformation["message"]->push($errorMessage);
            }
        }

        return redirect()->route("user.permission.group.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function userPermissionGroupDeleteValidation($slug){
        $userPermissionGroup = UserPermissionGroup::where("slug",$slug)->firstorFail();

        $statusInformation = array("status" => "errors","message" => collect());

        if($userPermissionGroup->users->count() > 0){
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Some user are using permission group.");
        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Pass the validation.");
        }
        return $statusInformation;
    }
}
