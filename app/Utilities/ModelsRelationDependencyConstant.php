<?php

namespace App\Utilities;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\SupplierAgent;
use App\Models\ContractCategory;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use App\Models\ProductAttributeValue;
use Spatie\Activitylog\Facades\LogBatch;

class ModelsRelationDependencyConstant
{
    // Get dependency records information for trash.
    public static function contractCategoryDependencyNeedToTrashRecordsInfo($slug){
        $records = array();
        $allCategoryIds = array();
        $pCategory = ContractCategory::where("slug",$slug)->first();
        if($pCategory){
            // Dependent product category.
            $pCategoryTreeIds = $pCategory->descendants()->pluck("id")->toArray();
            array_push($records,count($pCategoryTreeIds)." product category(ies) will be trashed according to dependency relation.");

            $allCategoryIds = $pCategoryTreeIds;
            array_push($allCategoryIds,$pCategory->id);
        }
        else{
            array_push($records,"Unknown product category for trash dependency.");
        }
        return $records;
    }

    // Get dependency records information for restore.
    public static function contractCategoryDependencyNeedToRestoreRecordsInfo($slug,$deletedAt){
        $records = array();
        $allCategoryIds = array();

        $pCategory = ContractCategory::onlyTrashed()->where("slug",$slug)->first();

        if($pCategory){
            // Dependent product category.
            $pCategoryTreeIds = $pCategory->descendantsWithTrashed()->where(DB::raw("(STR_TO_DATE(deleted_at,'%Y-%m-%d'))"),date('Y-m-d',strtotime($deletedAt)))->pluck("id")->toArray();
            array_push($records,count($pCategoryTreeIds)." product category(ies) will be restored according to dependency relation.");

            $allCategoryIds = $pCategoryTreeIds;
            array_push($allCategoryIds,$pCategory->id);
        }
        else{
            array_push($records,"Unknown product category for restore dependency.");
        }
        return $records;
    }


    //Trash all related logic.
    public static function contractCategoryTrashDependency($slug){
        $statusInformation = array("status" => "errors","message" => array());

        $allCategoryIds = array();
        $pCategory = ContractCategory::onlyTrashed()->where("slug",$slug)->first();

        if($pCategory){
            array_push($allCategoryIds,$pCategory->id);

            // Dependent product category.
            $pCategoryTreeIds = $pCategory->descendants()->pluck("id")->toArray();
            if(count($pCategoryTreeIds) > 0){
                $pCategoriesTrash=ContractCategory::whereIn("id",$pCategoryTreeIds)->delete();
                if($pCategoriesTrash){
                    $statusInformation["status"] = "status";
                    array_push($statusInformation["message"],count($pCategoryTreeIds)." dependent product category(ies) is/are trashed.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    array_push($statusInformation["message"],"Fail to trash dependent product category(ies).");
                }

                foreach($pCategoryTreeIds as $perPCTId){
                    if(in_array($perPCTId,$allCategoryIds) == false){
                        array_push($allCategoryIds,$perPCTId);
                    }
                }
            }
            else{
                $statusInformation["status"] = "status";
                array_push($statusInformation["message"],"No product category(ies) are dependent to be trashed.");
            }
        }
        else{
            array_push($statusInformation["message"],"Unknown product category for trash dependency.");
        }

        return $statusInformation;
    }


    // Restore all related logic.
    public static function contractCategoryRestoreDependency($slug,$deletedAt){
        $statusInformation = array("status" => "errors","message" => array());

        $allCategoryIds = array();
        $pCategory = ContractCategory::where("slug",$slug)->first();

        if($pCategory){
            $pCategoryTreeIds = $pCategory->descendantsWithTrashed()->where(DB::raw("(STR_TO_DATE(deleted_at,'%Y-%m-%d'))"),date('Y-m-d',strtotime($deletedAt)))->pluck("id")->toArray();
            array_push($allCategoryIds,$pCategory->id);

           // Dependent product category.
            if(count($pCategoryTreeIds) > 0){
                $pCategoriesRestore = ContractCategory::onlyTrashed()->whereIn("id",$pCategoryTreeIds)->restore();

                if($pCategoriesRestore){
                    $statusInformation["status"] = "status";
                    array_push($statusInformation["message"],count($pCategoryTreeIds)." dependent product category(ies) is/are restored.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    array_push($statusInformation["message"],"Fail to restore dependent product category(ies).");
                }

                foreach($pCategoryTreeIds as $perPCTId){

                    if(in_array($perPCTId,$allCategoryIds) == false){
                        array_push($allCategoryIds,$perPCTId);
                    }
                }
            }
            else{
                $statusInformation["status"] = "status";
                array_push($statusInformation["message"],"No product category(ies) is/are dependent to be restored.");
            }
        }
        else{
            array_push($statusInformation["message"],"Unknown product category for restore dependency.");
        }

        return $statusInformation;
    }


}
