<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
use App\Models\UserPermissionGroup;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Mail\EmailSendForUser;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Facades\LogBatch;
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
        $pagination = 5;

        $paginations = array(5,15,30,45,60,75,90,100);
        $searchFields = array("All","Name","Email","Mobile no");

        $activeUsers = User::orderby("name","asc");
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

                $searchUserField = (in_array( $request->search_field,$searchFields)) ? Str::lower(str_replace(" ","_",$request->search_field) ): null;

                if(!($searchUserField == null)){
                    switch ($request->selected_nav_tab) {
                        case "Active":
                            $activeUsers = $activeUsers->whereNotNull("id");

                            switch ($searchUserField) {
                                case "all":
                                    $activeUsers = $activeUsers->orWhere("name","like","%".$request->search."%")
                                                                    ->orWhere("email","like","%".$request->search."%")
                                                                    ->orWhere("mobile_no","like","%".$request->search."%");
                                break;

                                default:
                                    $activeUsers = $activeUsers->where($searchUserField,"like","%".$request->search."%");
                                break;
                            }
                        break;

                        case "Trash":
                            $trashUsers = $trashUsers->whereNotNull("id");

                            switch ($searchUserField) {
                                case "all":
                                    $trashUsers = $trashUsers->orWhere("name","like","%".$request->search."%")
                                                                    ->orWhere("email","like","%".$request->search."%")
                                                                    ->orWhere("mobile_no","like","%".$request->search."%");
                                break;

                                default:
                                    $trashUsers = $trashUsers->where($searchUserField,"like","%".$request->search."%");
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

    public function create(){
        $userRoles = array();
        $userPermissionGroups = UserPermissionGroup::orderBy("name","asc")->get();
        if(Auth::user()->hasUserPermission(["UMP02"]) == true){
            array_push($userRoles,"Owner");
        }

        if(Auth::user()->hasUserPermission(["UMP03"]) == true){
            array_push($userRoles,"Subordinate");
        }

        return view('internal user.user.create',compact("userRoles","userPermissionGroups"));
    }

    public function edit($slug){
        $userRoles = array();
        $userPermissionGroups = UserPermissionGroup::orderBy("name","asc")->get();

        if(Auth::user()->hasUserPermission(["UMP02"]) == true){
            array_push($userRoles,"Owner");
        }

        if(Auth::user()->hasUserPermission(["UMP03"]) == true){
            array_push($userRoles,"Subordinate");
        }

        $user = User::withTrashed()->where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail();

        return view('internal user.user.edit',compact("userRoles","userPermissionGroups","user"));
    }

    public function save(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'mobile_no' => 'nullable|max:20|regex:/^([0-9\s\-\+]*)$/|unique:users,mobile_no',
                'email' => 'required|email|max:255|unique:users,email',
                'user_role' => 'required|in:Owner,Subordinate',
                'auto_email_verify' => 'required|in:Yes,No',
                'default_password' => 'required|in:Yes,No',
                'password' => 'nullable|required_if:default_password,No|max:255',
                'user_permission_group' => 'nullable|required_if:user_role,Subordinate',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'mobile_no.max' => 'Mobile no length can not greater then 20 chars.',
                'mobile_no.regex' => 'Mobile no must be mobile no.',
                'mobile_no.unique' => 'Mobile no must be unique.',

                'email.required' => 'Email is required.',
                'email.max' => 'Email length can not greater then 255 chars.',
                'email.email' => 'Email must be email.',
                'email.unique' => 'Email must be unique.',

                'user_role.required' => 'User role is reqired.',
                'user_role.in' => 'User role must one out of [Owner,Subordinate].',

                'auto_email_verify.required' => 'Auto email verify is reqired.',
                'auto_email_verify.in' => 'Auto email verify must one out of [Yes,No].',

                'default_password.required' => 'Default password is reqired.',
                'default_password.in' => 'Default password must one out of [Yes,No].',

                'password.required_if' => 'Password is reqired.',
                'password.max' => 'Password length can not greater then 255 chars.',

                'user_permission_group.required_if' => 'User permission group is reqired.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();

            if($afterValidatorData["user_role"] == "Owner"){

                if(Auth::user()->hasUserPermission(["UMP02"]) == false){
                    $validator->errors()->add(
                        'user_role', "Your do not have permission to create user that have 'Owner' user role."
                    );
                }
            }

            if($afterValidatorData["user_role"] == "Subordinate"){

                if(Auth::user()->hasUserPermission(["UMP03"]) == false){
                    $validator->errors()->add(
                        'user_role', "Your do not have permission to create user that have 'Subordinate' user role."
                    );
                }

                if(!(array_key_exists("user_permission_group",$afterValidatorData) == true) && ( ($afterValidatorData["user_permission_group"]) > 0)){
                    $validUserPermissionGroup = 0;

                    foreach ($afterValidatorData["user_permission_group"] as $userPermissionGroup) {
                        if(UserPermissionGroup::where("slug",$userPermissionGroup)->count() > 0){
                            $validUserPermissionGroup = $validUserPermissionGroup + 1;
                        }
                    }

                    if(!($validUserPermissionGroup == count($afterValidatorData["user_permission_group"]))){
                        $validator->errors()->add(
                            'user_permission_group', "Some user permission group are unknown."
                        );
                    }
                }
            }

        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userPermissionGroupIds = array();
        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $user = new User();
            $user->name = $request->name;
            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            $user->user_role = $request->user_role;
            $user->email_verified_at = ($request->auto_email_verify == "Yes") ? Carbon::now() : null;
            $user->password = Hash::make(($request->default_password == "Yes") ? "123456789" : $request->password);
            $user->default_password = ($request->default_password == "Yes") ? 1 : 0;
            $user->slug = SystemConstant::slugGenerator($request->name,200);
            $user->created_at = Carbon::now();
            $user->created_by_id = Auth::user()->id;
            $user->updated_at = null;
            $saveUser = $user->save();
        LogBatch::endBatch();

        if($saveUser){

            if(($request->user_role == "Subordinate") && ($request->has("user_permission_group") && (count($request->user_permission_group) > 0))){
                foreach($request->user_permission_group as $perUserPermissionGroup){
                    $userPermissionGroup = UserPermissionGroup::where("slug",$perUserPermissionGroup)->firstOrFail();
                    array_push($userPermissionGroupIds,$userPermissionGroup->id);
                }

                $userPermissionGroupIds = SystemConstant::arraySort($userPermissionGroupIds,"Value","Asc");

                if(count($userPermissionGroupIds) > 0){
                    $user->userPermissionGroups()->attach(
                        $userPermissionGroupIds,["created_at" => Carbon::now(),"updated_at" => null,"created_by_id" => Auth::user()->id]
                    );
                }

                if($user->userPermissionGroups()->count() == count($request->user_permission_group)){
                    $statusInformation["message"]->push("All selected user permission groups are sync.");
                }
                else{
                    $statusInformation["status"] = "warning";
                    $statusInformation["message"]->push("Selected user permission groups are fail to sync.");
                }
            }

            $this->sendNotification("Create","A new user has been created by ".Auth::user()->name.".",$user);
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully created.");

            if($request->auto_email_verify == "No"){
                $user->sendEmailVerificationNotification();
                $statusInformation["message"]->push("Please asked to verify email before is expired.");
            }

            if($request->default_password == "Yes"){
                $statusInformation["message"]->push("Default pasword (123456789) is used for password.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to create.";
        }

        return redirect()->route("user.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function update(Request $request,$slug){
        $userId = User::where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail()->id;
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'mobile_no' => 'nullable|max:20|regex:/^([0-9\s\-\+]*)$/|unique:users,mobile_no,'.$userId,
                'update_email' => 'required|in:Yes,No',
                'auto_email_verify' => 'nullable|required_if:update_email,Yes|in:Yes,No',
                'email' => 'nullable|required_if:update_email,Yes|email|max:255|unique:users,email,'.$userId,
                'user_role' => 'required|in:Owner,Subordinate',
                'reset_password' => 'required|in:Yes,No',
                'default_password' => 'nullable|required_if:reset_password,Yes|in:Yes,No',
                'password' => 'nullable|required_if:default_password,No|max:255',
                'user_permission_group' => 'nullable|required_if:user_role,Subordinate',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'mobile_no.max' => 'Mobile no length can not greater then 20 chars.',
                'mobile_no.regex' => 'Mobile no must be mobile no.',
                'mobile_no.unique' => 'Mobile no must be unique.',

                'update_email.required' => 'Update email is reqired.',
                'update_email.in' => 'Update email must one out of [Yes,No].',

                'email.required_if' => 'Email is required.',
                'email.max' => 'Email length can not greater then 255 chars.',
                'email.email' => 'Email must be email.',
                'email.unique' => 'Email must be unique.',

                'auto_email_verify.required_if' => 'Auto email verify is reqired.',
                'auto_email_verify.in' => 'Auto email verify must one out of [Yes,No].',

                'user_role.required' => 'User role is reqired.',
                'user_role.in' => 'User role must one out of [Owner,Subordinate].',

                'default_password.required_if' => 'Default password is reqired.',
                'default_password.in' => 'Default password must one out of [Yes,No].',

                'reset_password.required' => 'Reset password is reqired.',
                'reset_password.in' => 'Reset password must one out of [Yes,No].',

                'password.required_if' => 'Password is reqired.',
                'password.max' => 'Password length can not greater then 255 chars.',

                'user_permission_group.required_if' => 'User permission group is reqired.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();

            if($afterValidatorData["user_role"] == "Owner"){

                if(Auth::user()->hasUserPermission(["UMP02"]) == false){
                    $validator->errors()->add(
                        'user_role', "Your do not have permission to create user that have 'Owner' user role."
                    );
                }
            }

            if($afterValidatorData["user_role"] == "Subordinate"){

                if(Auth::user()->hasUserPermission(["UMP03"]) == false){
                    $validator->errors()->add(
                        'user_role', "Your do not have permission to create user that have 'Subordinate' user role."
                    );
                }

                if(!(array_key_exists("user_permission_group",$afterValidatorData) == true) && ( ($afterValidatorData["user_permission_group"]) > 0)){
                    $validUserPermissionGroup = 0;

                    foreach ($afterValidatorData["user_permission_group"] as $userPermissionGroup) {
                        if(UserPermissionGroup::where("slug",$userPermissionGroup)->count() > 0){
                            $validUserPermissionGroup = $validUserPermissionGroup + 1;
                        }
                    }

                    if(!($validUserPermissionGroup == count($afterValidatorData["user_permission_group"]))){
                        $validator->errors()->add(
                            'user_permission_group', "Some user permission group are unknown."
                        );
                    }
                }
            }

        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $doneUpdateEmail = false;
        $userPermissionGroupIds = array();

        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $user = User::where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail();
            $user->name = $request->name;
            $user->mobile_no = $request->mobile_no;
            $user->user_role = $request->user_role;

            if( $request->update_email == "Yes"){
                if(!($user->email == $request->email)){
                    $doneUpdateEmail = true;
                    $user->email = $request->email;
                    $user->email_verified_at = ($request->auto_email_verify == "Yes") ? Carbon::now() : null;
                }
            }

            if($request->reset_password == "Yes"){
                $user->password = Hash::make(($request->default_password == "Yes") ? "123456789" : $request->password);
                $user->default_password = ($request->default_password == "Yes") ? 1 : 0;
            }

            $user->slug = SystemConstant::slugGenerator($request->name,200);

            $user->updated_at = Carbon::now();
            $updateUser = $user->update();
        LogBatch::endBatch();

        if($updateUser){
            if(($request->user_role == "Subordinate") && ($request->has("user_permission_group") && (count($request->user_permission_group) > 0))){
                foreach($request->user_permission_group as $perUserPermissionGroup){
                    $userPermissionGroup = UserPermissionGroup::where("slug",$perUserPermissionGroup)->firstOrFail();
                    array_push($userPermissionGroupIds,$userPermissionGroup->id);
                }

                $userPermissionGroupIds = SystemConstant::arraySort($userPermissionGroupIds,"Value","Asc");

                if(count($userPermissionGroupIds) > 0 ){
                    $syncUserPermissionGroupData = array();
                    foreach($userPermissionGroupIds as $perUPGId){
                        $syncUserPermissionGroupData[$perUPGId] = ["created_at" => Carbon::now(),"updated_at" => Carbon::now(),"created_by_id" => Auth::user()->id];
                    }

                    $user->userPermissionGroups()->sync($syncUserPermissionGroupData);
                }

                if($user->userPermissionGroups()->count() == count($request->user_permission_group)){
                    $statusInformation["message"]->push("Selected user permission groups are syn.");
                }
                else{
                    $statusInformation["status"] = "warning";
                    $statusInformation["message"]->push("Some selected user permission groups are fail to syn.");
                }
            }

            $this->sendNotification("Update","A user has been updated by ".Auth::user()->name.".",$user);
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully updated.");

            if(($request->user_role == "Owner")){
                foreach($user->userPermissionGroups as $perUPG){
                    $user->userPermissionGroups()->detach([$perUPG->id]);
                }
            }

            if(($request->update_email == "Yes") && ($doneUpdateEmail == true)){
                $statusInformation["message"]->push("Email has been updated.");

                if($request->auto_email_verify == "No"){
                    $user->sendEmailVerificationNotification();
                    $statusInformation["message"]->push("Please asked to verify email before is expired.");
                }
            }

            if($request->reset_password == "Yes"){
                $statusInformation["message"]->push("Password reset is done.");

                if($request->default_password == "Yes"){
                    $statusInformation["message"]->push("Default pasword (123456789) is used for password.");
                }
            }

        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to update.";
        }

        return redirect()->route("user.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function trash($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if((User::onlyTrashed()->where("slug",$slug)->whereNot("id",Auth::user()->id)->count()) == 0){
            if($this->trashUserValidationCheck($slug) == true){

                LogBatch::startBatch();
                    $user = User::where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail();
                    $trashedUser = $user->delete();
                LogBatch::endBatch();

                if($trashedUser){
                    $this->sendNotification("Trash","A user has been updated by ".Auth::user()->name.".",$user);

                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push( "Successfully trashed.");
                }
                else{
                    $statusInformation["message"]->push("Fail to trash.");
                }
            }
            else{
                $statusInformation["message"]->push( "You does not have parmission to trash.");
            }

        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Already trashed.");
        }

        return redirect()->route("user.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function restore($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if((User::where("slug",$slug)->whereNot("id",Auth::user()->id)->count()) == 0){
            if($this->restoreUserValidationCheck($slug) == true){

                LogBatch::startBatch();
                    $user = User::onlyTrashed()->where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail();
                    $restoreUser = $user->restore();
                LogBatch::endBatch();

                if($restoreUser){
                    $this->sendNotification("Trash","A user has been updated by ".Auth::user()->name.".",$user);

                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push( "Successfully restore.");
                }
                else{
                    $statusInformation["message"]->push( "Fail to restore.");
                }
            }
            else{
                $statusInformation["message"]->push( "You does not have parmission to restore.");
            }
        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push( "Already active.");
        }

        return redirect()->route("user.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function trashUserValidationCheck($slug){
        $restoreAableUser = false;
        $user = User::where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail();

        if(($user->user_role == "Owner") && (Auth::user()->hasUserPermission(["UMP07"]) == true)){
            $restoreAableUser = true;
        }

        if(($user->user_role == "Subordinate") && (Auth::user()->hasUserPermission(["UMP08"]) == true)){
            $restoreAableUser = true;
        }

        return $restoreAableUser;
    }

    private function restoreUserValidationCheck($slug){
        $restoreAableUser = false;
        $user =  User::onlyTrashed()->where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail();

        if(($user->user_role == "Owner") && (Auth::user()->hasUserPermission(["UMP09"]) == true)){
            $restoreAableUser = true;
        }

        if(($user->user_role == "Subordinate") && (Auth::user()->hasUserPermission(["UMP10"]) == true)){
            $restoreAableUser = true;
        }

        return $restoreAableUser;
    }

    private function sendNotification($event,$subject,User $user){
        $envelope = array();

        $notificationSetting = Setting::where( 'code','NotificationSetting')->firstOrFail()->fields_with_values["User"];

        $envelope["to"] = $notificationSetting["to"];
        $envelope["cc"] = $notificationSetting["cc"];
        $envelope["from"] = $notificationSetting["from"];
        $envelope["reply"] = $notificationSetting["reply"];

        if(($notificationSetting["send"] == true) && (($notificationSetting["event"] == "All") || (!($notificationSetting["event"] == "All") && ($notificationSetting["event"] == $event)))){
            Mail::send(new EmailSendForUser($envelope,$subject,$user));
        }
    }
}
