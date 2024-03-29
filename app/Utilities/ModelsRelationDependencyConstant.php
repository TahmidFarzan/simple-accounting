<?php

namespace App\Utilities;

use Carbon\Carbon;
use App\Models\ProjectContractCategory;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\LogBatch;

class ModelsRelationDependencyConstant
{
    // Get dependency records information for trash.
    public static function projectContractCategoryDependencyNeedToTrashRecordsInfo($slug){
        $records = array();
        $allCategoryIds = array();
        $pCategory = ProjectContractCategory::where("slug",$slug)->first();
        if($pCategory){
            // Dependent category.
            $pCategoryTreeIds = $pCategory->descendants()->pluck("id")->toArray();
            array_push($records,count($pCategoryTreeIds)." category(ies) will be trashed according to dependency relation.");

            $allCategoryIds = $pCategoryTreeIds;
            array_push($allCategoryIds,$pCategory->id);
        }
        else{
            array_push($records,"Unknown category for trash dependency.");
        }
        return $records;
    }

    // Get dependency records information for restore.
    public static function projectContractCategoryDependencyNeedToRestoreRecordsInfo($slug,$deletedAt){
        $records = array();
        $allCategoryIds = array();

        $pCategory = ProjectContractCategory::onlyTrashed()->where("slug",$slug)->first();

        if($pCategory){
            // Dependent category.
            $pCategoryTreeIds = $pCategory->descendantsWithTrashed()->where(DB::raw("(STR_TO_DATE(deleted_at,'%Y-%m-%d'))"),date('Y-m-d',strtotime($deletedAt)))->pluck("id")->toArray();
            array_push($records,count($pCategoryTreeIds)." category(ies) will be restored according to dependency relation.");

            $allCategoryIds = $pCategoryTreeIds;
            array_push($allCategoryIds,$pCategory->id);
        }
        else{
            array_push($records,"Unknown category for restore dependency.");
        }
        return $records;
    }

    //Trash all related logic.
    public static function projectContractCategoryTrashDependency($slug){
        $statusInformation = array("status" => "errors","message" => array());

        $allCategoryIds = array();
        $pCategory = ProjectContractCategory::onlyTrashed()->where("slug",$slug)->first();

        if($pCategory){
            array_push($allCategoryIds,$pCategory->id);

            // Dependent category.
            $pCategoryTreeIds = $pCategory->descendants()->pluck("id")->toArray();
            if(count($pCategoryTreeIds) > 0){
                $pCategoriesTrash = ProjectContractCategory::whereIn("id",$pCategoryTreeIds)->delete();
                if($pCategoriesTrash){
                    $statusInformation["status"] = "status";
                    array_push($statusInformation["message"],count($pCategoryTreeIds)." dependent category(ies) is/are trashed.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    array_push($statusInformation["message"],"Fail to trash dependent category(ies).");
                }

                foreach($pCategoryTreeIds as $perPCTId){
                    if(in_array($perPCTId,$allCategoryIds) == false){
                        array_push($allCategoryIds,$perPCTId);
                    }
                }
            }
            else{
                $statusInformation["status"] = "status";
                array_push($statusInformation["message"],"No category(ies) are dependent to be trashed.");
            }
        }
        else{
            array_push($statusInformation["message"],"Unknown category for trash dependency.");
        }

        return $statusInformation;
    }

    // Restore all related logic.
    public static function projectContractCategoryRestoreDependency($slug,$deletedAt){
        $statusInformation = array("status" => "errors","message" => array());

        $allCategoryIds = array();
        $pCategory = ProjectContractCategory::where("slug",$slug)->first();

        if($pCategory){
            $pCategoryTreeIds = $pCategory->descendantsWithTrashed()->where(DB::raw("(STR_TO_DATE(deleted_at,'%Y-%m-%d'))"),date('Y-m-d',strtotime($deletedAt)))->pluck("id")->toArray();
            array_push($allCategoryIds,$pCategory->id);

           // Dependent category.
            if(count($pCategoryTreeIds) > 0){
                $pCategoriesRestore = ProjectContractCategory::onlyTrashed()->whereIn("id",$pCategoryTreeIds)->restore();

                if($pCategoriesRestore){
                    $statusInformation["status"] = "status";
                    array_push($statusInformation["message"],count($pCategoryTreeIds)." dependent category(ies) is/are restored.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    array_push($statusInformation["message"],"Fail to restore dependent category(ies).");
                }

                foreach($pCategoryTreeIds as $perPCTId){

                    if(in_array($perPCTId,$allCategoryIds) == false){
                        array_push($allCategoryIds,$perPCTId);
                    }
                }
            }
            else{
                $statusInformation["status"] = "status";
                array_push($statusInformation["message"],"No category(ies) is/are dependent to be restored.");
            }
        }
        else{
            array_push($statusInformation["message"],"Unknown category for restore dependency.");
        }

        return $statusInformation;
    }

}
