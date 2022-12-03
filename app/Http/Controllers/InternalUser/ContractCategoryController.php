<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ContractCategory;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Utilities\ModelsRelationDependencyConstant;

class ContractCategoryController extends Controller
{
    private $slug;

    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:CCMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:CCMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:CCMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:CCMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:CCMP05'])->only(["trash"]);
        $this->middleware(['user.user.permission.check:CCMP06'])->only(["restore"]);
    }

    public function index(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $contractCategoriesTree = ContractCategory::withTrashed()->tree()->get()->toTree();

        $activeContractCategories = ContractCategory::orderby("created_at","desc");
        $trashContractCategories = ContractCategory::onlyTrashed()->orderby("created_at","desc");


        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('contract_category') && !($request->contract_category == null) && !($request->contract_category == "All")){
                $contractCategory = ContractCategory::where("slug",$request->contract_category)->first();
                switch ($request->selected_nav_tab) {
                    case 'Active':
                        $activeContractCategories = $activeContractCategories->whereIn("id",$contractCategory->descendantsAndSelf()->pluck("id"));
                    break;

                    case 'Trash':
                        $trashContractCategories = $trashContractCategories->whereIn("id",$contractCategory->descendantsAndSelf()->pluck("id"));
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

        return view('internal user.extra.contract category.index',compact("activeContractCategories","trashContractCategories","paginations","contractCategoriesTree"));
    }

    public function create(){
        $contractCategories = ContractCategory::tree()->get()->toTree();
        return view('internal user.extra.contract category.create',compact("contractCategories"));
    }

    public function details($slug){
        $contractCategory = ContractCategory::withTrashed()->where("slug",$slug)->firstOrFail();

        $contractCategoryConstraint = function ($query) use ($contractCategory) {
            $query->where('id', $contractCategory->id);
        };

        $contractCategoryTree = ContractCategory::treeOf(function ($query) use ($contractCategory) {
            $query->where('id', $contractCategory->id);
        })->get()->toTree();

        return view('internal user.extra.contract category.details',compact("contractCategory","contractCategoryTree"));
    }

    public function edit($slug){
        $contractCategories = ContractCategory::withTrashed()->tree()->get()->toTree();
        $contractCategory = ContractCategory::withTrashed()->where("slug",$slug)->firstOrFail();
        return view('internal user.extra.contract category.edit',compact("contractCategories","contractCategory"));
    }

    public function save(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'description' => 'nullable',
                'code' => 'required|max:200|unique:contract_categories,code',
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
            $contractCategoryFound = ContractCategory::withTrashed()->where(Str::lower("name"),Str::lower($afterValidatorData["name"]));

            if(Str::upper($afterValidatorData["has_a_parent"]) == "YES"){
                if(array_key_exists('parent', $afterValidatorData) && !($afterValidatorData["parent"] == null)){
                    $parentCategory = ContractCategory::withTrashed()->where("slug",$afterValidatorData["parent"])->first();
                    if($parentCategory){
                        $contractCategoryFound = ($contractCategoryFound->where("parent_id",$parentCategory->id))->count();
                        if($contractCategoryFound > 0){
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
                $contractCategoryFound = $contractCategoryFound->count();
                if($contractCategoryFound >0 ){
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
            $contractCategory= new ContractCategory();
            $contractCategory->name = $request->name;
            $contractCategory->code = $request->code;
            $contractCategory->description = $request->description;
            $contractCategory->parent_id = (Str::upper($request->has_a_parent) == "YES") ? ContractCategory::where("slug",$request->parent)->firstOrFail()->id : null;
            $contractCategory->slug = SystemConstant::slugGenerator($request->name,200);
            $contractCategory->created_at = Carbon::now();
            $contractCategory->created_by_id = Auth::user()->id;
            $contractCategory->updated_at = null;
            $saveContractCategory = $contractCategory->save();
        LogBatch::endBatch();

        if($saveContractCategory){
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
                'code' => 'required|max:200|unique:contract_categories,code,'.(ContractCategory::withTrashed()->where("slug",$slug)->firstOrFail())->id,
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
            $contractCategoryFound = ContractCategory::withTrashed()->where(Str::lower("name"),Str::lower($afterValidatorData["name"]));

            if(Str::upper($afterValidatorData["has_a_parent"]) == "YES"){
                if(array_key_exists('parent', $afterValidatorData) && !($afterValidatorData["parent"] == null)){
                    $parentCategory = ContractCategory::withTrashed()->where("slug",$afterValidatorData["parent"])->first();
                    if($parentCategory){
                        $contractCategoryFound = ($contractCategoryFound->where("parent_id",$parentCategory->id)->whereNot('slug',$this->slug))->count();
                        if($contractCategoryFound > 0){
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
                $contractCategoryFound = $contractCategoryFound->whereNot('slug',$this->slug)->count();
                if($contractCategoryFound > 0){
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
            $contractCategory = ContractCategory::withTrashed()->where("slug",$slug)->firstOrFail();
            $contractCategory->name = $request->name;
            $contractCategory->code = $request->code;
            $contractCategory->description = $request->description;
            $contractCategory->parent_id = (Str::upper($request->has_a_parent) == "YES") ? ContractCategory::withTrashed()->where("slug",$request->parent)->firstOrFail()->id : null;
            $contractCategory->slug = SystemConstant::slugGenerator($request->name,200);
            $contractCategory->updated_at = Carbon::now();
            $updateContractCategory = $contractCategory->update();
        LogBatch::endBatch();

        if($updateContractCategory){
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

        if((ContractCategory::onlyTrashed()->where("slug",$slug)->count()) == 0){

            LogBatch::startBatch();
                $contractCategory = ContractCategory::where("slug",$slug)->firstOrFail();
                $trashedContractCategory = $contractCategory->delete();
                $trashDependencyStatusInformation = ModelsRelationDependencyConstant::contractCategoryTrashDependency($slug);
            LogBatch::endBatch();

            if($trashedContractCategory){
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

        if((ContractCategory::where("slug",$slug)->count()) == 0){
            $restoreValidation = $this->restoreValidation($slug);
            if($restoreValidation["status"] == "status"){
                LogBatch::startBatch();
                    $contractCategory = ContractCategory::onlyTrashed()->where("slug",$slug)->firstOrFail();
                    $deletedAt = $contractCategory->deleted_at;
                    $restoreContractCategory = $contractCategory->restore();
                    $restoreDependencyStatusInformation = ModelsRelationDependencyConstant::contractCategoryRestoreDependency($slug,$deletedAt);
                LogBatch::endBatch();

                if($restoreContractCategory){
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
        $contractCategory = ContractCategory::onlyTrashed()->where("slug",$slug)->firstOrFail();

        if(!($contractCategory->parent_id == null)){

            $pCAncestors = $contractCategory->ancestorsWithTrashed()->whereNotNull("deleted_at");

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
