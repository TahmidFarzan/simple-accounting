<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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

        if(Auth::user()->hasUserPermission(["UMP02"]) == true){
            array_push($userRoles,"Owner");
        }

        if(Auth::user()->hasUserPermission(["UMP03"]) == true){
            array_push($userRoles,"Subordinate");
        }

        return view('internal user.user.create',compact("userRoles"));
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
                'password.max' => 'Password length can not greater then 255 chars..',
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
            }

        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => array());

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

        if($saveUser){
            $statusInformation["status"] = "status";
            array_push($statusInformation["message"],"User successfully created.");

            if($request->auto_email_verify == "No"){
                $user->sendEmailVerificationNotification();
                array_push($statusInformation["message"],"Please asked user to verify email before is expired.");
            }

            if($request->default_password == "Yes"){
                array_push($statusInformation["message"],"Default pasword(123456789) is used for user password.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to create user.";
        }

        return redirect()->route("user.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function trash($slug){
        $statusInformation = array("status" => "errors","message" => array());

        if((User::onlyTrashed()->where("slug",$slug)->whereNot("id",Auth::user()->id)->count()) == 0){
            if($this->trashUserValidationCheck($slug) == true){
                $user = User::where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail();
                $trashedUser = $user->delete();

                if($trashedUser){
                    $statusInformation["status"] = "status";
                    array_push($statusInformation["message"], "User successfully trashed.");
                }
                else{
                    array_push($statusInformation["message"], "User fail to trash.");
                }
            }
            else{
                array_push($statusInformation["message"], "You does not have parmission to trash user.");
            }

        }
        else{
            $statusInformation["status"] = "status";
            array_push($statusInformation["message"], "User already trashed.");
        }

        return redirect()->route("user.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function restore($slug){
        $statusInformation = array("status" => "errors","message" => array());

        if((User::where("slug",$slug)->whereNot("id",Auth::user()->id)->count()) == 0){
            if($this->restoreUserValidationCheck($slug) == true){
                $user = User::onlyTrashed()->where("slug",$slug)->whereNot("id",Auth::user()->id)->firstOrFail();
                $restoreUser = $user->restore();
                if($restoreUser){
                    $statusInformation["status"] = "status";
                    array_push($statusInformation["message"], "User successfully restore.");
                }
                else{
                    array_push($statusInformation["message"], "User fail to restore.");
                }
            }
            else{
                array_push($statusInformation["message"], "You does not have parmission to restore user.");
            }
        }
        else{
            $statusInformation["status"] = "status";
            array_push($statusInformation["message"], "User already active.");
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
}
