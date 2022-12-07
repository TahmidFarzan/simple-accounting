<?php

namespace App\Http\Controllers\InternalUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectContractPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCPMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCPMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCPMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCPMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:PCPMP05'])->only(["delete"]);
    }
}
