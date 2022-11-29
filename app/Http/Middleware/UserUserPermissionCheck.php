<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserUserPermissionCheck
{
    public function handle(Request $request, Closure $next,...$requiredUserPermissionCodes)
    {
        if($this->checkUserPermission($requiredUserPermissionCodes) == true){
            return $next($request);
        }
        else{
            return redirect("dashboard")->with(['warning'=>'You are not authorise to access this.']);
        }
    }

    private function checkUserPermission($requiredUserPermissionCodes){
        $userHasUserPermission = false;

        foreach($requiredUserPermissionCodes as $perRequiredUserPermissionCode){
            if(Auth::user()->hasUserPermission([$perRequiredUserPermissionCode]) == true){
                $userHasUserPermission=true;
            }
        }

        return $userHasUserPermission;
    }
}
