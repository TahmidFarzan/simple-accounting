<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProjectContractCategory;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Utilities\ModelsRelationDependencyConstant;

class ProjectContractCategoryController extends Controller
{
    private $slug;

    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCCMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCCMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCCMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCCMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:PCCMP05'])->only(["trash"]);
        $this->middleware(['user.user.permission.check:PCCMP06'])->only(["restore"]);
    }

    public function index(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $contractCategoriesTree = ProjectContractCategory::withTrashed()->tree()->get()->toTree();

        $activeContractCategories = ProjectContractCategory::orderby("created_at","desc");
        $trashContractCategories = ProjectContractCategory::onlyTrashed()->orderby("created_at","desc");


        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('contract_category') && !($request->contract_category == null) && !($request->contract_category == "All")){
                $projectContractCategory = ProjectContractCategory::where("slug",$request->contract_category)->first();
                switch ($request->selected_nav_tab) {
                    case 'Active':
                        $activeContractCategories = $activeContractCategories->whereIn("id",$projectContractCategory->descendantsAndSelf()->pluck("id"));
                    break;

                    case 'Trash':
                        $trashContractCategories = $trashContractCategories->whereIn("id",$projectContractCategory->descendantsAndSelf()->pluck("id"));
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }

            if($request->has('search') && !($request->search == null)){
                switch ($request->selected_nav_tab) {
                    case 'Active':
                        $activeContractCategories = $activeContractCategories->where("name","like","%".$request->search."%")
                                                                        ->orWhere("code","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
                    break;

                    case 'Trash':
                        $trashContractCategories = $trashContractCategories->where("name","like","%".$request->search."%")
                                                                        ->orWhere("code","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }
        }
        $trashContractCategories = $trashContractCategories->paginate($pagination);
        $activeContractCategories = $activeContractCategories->paginate($pagination);

        return view('internal user.project contract.category.index',compact("activeContractCategories","trashContractCategories","paginations","contractCategoriesTree"));
    }

    public function create(){
        $contractCategories = ProjectContractCategory::tree()->get()->toTree();
        return view('internal user.project contract.category.create',compact("contractCategories"));
    }

    public function details($slug){
        $projectContractCategory = ProjectContractCategory::withTrashed()->where("slug",$slug)->firstOrFail();

        $projectContractCategoryConstraint = function ($query) use ($projectContractCategory) {
            $query->where('id', $projectContractCategory->id);
        };

        $projectContractCategoryTree = ProjectContractCategory::treeOf(function ($query) use ($projectContractCategory) {
            $query->where('id', $projectContractCategory->id);
        })->get()->toTree();

        return view('internal user.project contract.category.details',compact("projectContractCategory","projectContractCategoryTree"));
    }

    public function edit($slug){
        $contractCategories = ProjectContractCategory::withTrashed()->tree()->get()->toTree();
        $projectContractCategory = ProjectContractCategory::withTrashed()->where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.category.edit',compact("contractCategories","projectContractCategory"));
    }

    public function save(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'description' => 'nullable',
                'code' => 'required|max:200|unique:project_contract_categories,code',
                'has_a_parent' => 'required|string|in:Yes,No',
                'parent' => 'nullable|string|required_if:has_a_parent,Yes',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'code.required' => 'Code is required.',
                'code.max' => 'Code length can not greater then 200 chars.',
                'code.unique' => 'Code must be unique.',

                'has_a_parent.required' => 'Has a parent contract category is required.',
                'has_a_parent.string' => 'Has a parent contract category must be a string.',
                'has_a_parent.in' => 'Has a parent contract category must be one out of [Yes,No].',

                'parent.string' => 'Parent must be a string.',
                'parent.required_if' => 'Parent must is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $projectContractCategoryFound = ProjectContractCategory::withTrashed()->where(Str::lower("name"),Str::lower($afterValidatorData["name"]));

            if(Str::upper($afterValidatorData["has_a_parent"]) == "YES"){
                if(array_key_exists('parent', $afterValidatorData) && !($afterValidatorData["parent"] == null)){
                    $parentCategory = ProjectContractCategory::withTrashed()->where("slug",$afterValidatorData["parent"])->first();
                    if($parentCategory){
                        $projectContractCategoryFound = ($projectContractCategoryFound->where("parent_id",$parentCategory->id))->count();
                        if($projectContractCategoryFound > 0){
                            $validator->errors()->add(
                                'name', "Same category exit for parent contract category."
                            );
                        }
                    }
                    else{
                        $validator->errors()->add(
                            'parent', "Unknown parent contract category."
                        );
                    }
                }
            }

            if(Str::upper($afterValidatorData["has_a_parent"]) == "NO"){
                $projectContractCategoryFound = $projectContractCategoryFound->count();
                if($projectContractCategoryFound >0 ){
                    $validator->errors()->add(
                        'name', "Same parent contract category exit."
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation=array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $projectContractCategory= new ProjectContractCategory();
            $projectContractCategory->name = $request->name;
            $projectContractCategory->code = $request->code;
            $projectContractCategory->description = $request->description;
            $projectContractCategory->parent_id = (Str::upper($request->has_a_parent) == "YES") ? ProjectContractCategory::where("slug",$request->parent)->firstOrFail()->id : null;
            $projectContractCategory->slug = SystemConstant::slugGenerator($request->name,200);
            $projectContractCategory->created_at = Carbon::now();
            $projectContractCategory->created_by_id = Auth::user()->id;
            $projectContractCategory->updated_at = null;
            $saveProjectContractCategory = $projectContractCategory->save();
        LogBatch::endBatch();

        if($saveProjectContractCategory){
            $statusInformation["status"] = "status";
            $statusInformation["message"] = "Contract category successfully created.";
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to create contract category.";
        }

        return redirect()->route("contract.category.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function update(Request $request,$slug){
        $this->slug = $slug;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'description' => "nullable",
                'code' => 'required|max:200|unique:project_contract_categories,code,'.(ProjectContractCategory::withTrashed()->where("slug",$slug)->firstOrFail())->id,
                'has_a_parent' => 'required|string|in:Yes,No',
                'parent' => 'nullable|string|required_if:has_a_parent,Yes',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'code.required' => 'Code is required.',
                'code.max' => 'Code length can not greater then 200 chars.',
                'code.unique' => 'Code must be unique.',

                'has_a_parent.required' => 'Has a parent contract category is required.',
                'has_a_parent.string' => 'Has a parent contract category must be a string.',
                'has_a_parent.in' => 'Has a parent contract category must be one out of [Yes,No].',

                'parent.string' => 'Parent must be a string.',
                'parent.required_if' => 'Parent must is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $projectContractCategoryFound = ProjectContractCategory::withTrashed()->where(Str::lower("name"),Str::lower($afterValidatorData["name"]));

            if(Str::upper($afterValidatorData["has_a_parent"]) == "YES"){
                if(array_key_exists('parent', $afterValidatorData) && !($afterValidatorData["parent"] == null)){
                    $parentCategory = ProjectContractCategory::withTrashed()->where("slug",$afterValidatorData["parent"])->first();
                    if($parentCategory){
                        $projectContractCategoryFound = ($projectContractCategoryFound->where("parent_id",$parentCategory->id)->whereNot('slug',$this->slug))->count();
                        if($projectContractCategoryFound > 0){
                            $validator->errors()->add(
                                'name', "Same category exit for parent contract category."
                            );
                        }
                    }
                    else{
                        $validator->errors()->add(
                            'parent', "Unknown parent contract category."
                        );
                    }
                }
            }

            if(Str::upper($afterValidatorData["has_a_parent"]) == "NO"){
                $projectContractCategoryFound = $projectContractCategoryFound->whereNot('slug',$this->slug)->count();
                if($projectContractCategoryFound > 0){
                    $validator->errors()->add(
                        'name', "Same parent contract category exit."
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation=array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $projectContractCategory = ProjectContractCategory::withTrashed()->where("slug",$slug)->firstOrFail();
            $projectContractCategory->name = $request->name;
            $projectContractCategory->code = $request->code;
            $projectContractCategory->description = $request->description;
            $projectContractCategory->parent_id = (Str::upper($request->has_a_parent) == "YES") ? ProjectContractCategory::withTrashed()->where("slug",$request->parent)->firstOrFail()->id : null;
            $projectContractCategory->slug = SystemConstant::slugGenerator($request->name,200);
            $projectContractCategory->updated_at = Carbon::now();
            $updateProjectContractCategory = $projectContractCategory->update();
        LogBatch::endBatch();

        if($updateProjectContractCategory){
            $statusInformation["status"] = "status";
            $statusInformation["message"] = "Contract category successfully updated.";
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to update contract category.";
        }

        return redirect()->route("contract.category.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function trash($slug){
        $statusInformation = array("status" => "errors","message" => collect());
        $trashDependencyStatusInformation = array();

        if((ProjectContractCategory::onlyTrashed()->where("slug",$slug)->count()) == 0){

            LogBatch::startBatch();
                $projectContractCategory = ProjectContractCategory::where("slug",$slug)->firstOrFail();
                $trashedProjectContractCategory = $projectContractCategory->delete();
                $trashDependencyStatusInformation = ModelsRelationDependencyConstant::projectContractCategoryTrashDependency($slug);
            LogBatch::endBatch();

            if($trashedProjectContractCategory){
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Contract category successfully trashed.");

                $statusInformation["status"] = $trashDependencyStatusInformation["status"];
                foreach($trashDependencyStatusInformation["message"] as $perMessages){
                    $statusInformation["message"]->push($perMessages);
                }
            }
            else{
                $statusInformation["message"]->push("Contract category fail to trash.");
            }
        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Contract category already trashed.");
        }

        return redirect()->route("contract.category.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function restore($slug){
        $statusInformation = array("status" => "errors","message" => collect());
        $restoreDependencyStatusInformation = array();

        if((ProjectContractCategory::where("slug",$slug)->count()) == 0){
            $restoreValidation = $this->restoreValidation($slug);
            if($restoreValidation["status"] == "status"){
                LogBatch::startBatch();
                    $projectContractCategory = ProjectContractCategory::onlyTrashed()->where("slug",$slug)->firstOrFail();
                    $deletedAt = $projectContractCategory->deleted_at;
                    $restoreProjectContractCategory = $projectContractCategory->restore();
                    $restoreDependencyStatusInformation = ModelsRelationDependencyConstant::projectContractCategoryRestoreDependency($slug,$deletedAt);
                LogBatch::endBatch();

                if($restoreProjectContractCategory){
                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push("Contract category successfully restored.");

                    $statusInformation["status"] = $restoreDependencyStatusInformation["status"];
                    foreach($restoreDependencyStatusInformation["message"] as $perMessages){
                        $statusInformation["message"]->push($perMessages);
                    }
                }
                else{
                    $statusInformation["message"]->push("Contract category fail to restore.");
                }

            }
            else{
                $statusInformation["status"] = $restoreValidation["status"];
                $statusInformation["message"] = $restoreValidation["message"];
            }
        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Contract category already actived.");
        }

        return redirect()->route("contract.category.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function restoreValidation($slug){
        $statusInformation = array("status" => "errors","message" => collect());
        $projectContractCategory = ProjectContractCategory::onlyTrashed()->where("slug",$slug)->firstOrFail();

        if(!($projectContractCategory->parent_id == null)){

            $pCAncestors = $projectContractCategory->ancestorsWithTrashed()->whereNotNull("deleted_at");

            if( $pCAncestors->count() > 0){
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Restore validation fail. Some ancestors contract category ".$pCAncestors->pluck("name")." are trashed.");
            }
            else{
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Restore validation pass. All ancestors contract category are active.");
            }

        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Restore validation pass. This contract category is  one of the root category.");
        }

        return $statusInformation;
    }
}