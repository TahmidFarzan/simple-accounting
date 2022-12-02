@extends('layouts.app')

@section('mainPageName')
    Setting
@endsection

@section('mainCardTitle')
    Business setting details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("setting.index") }}">Setting</a></li>
            <li class="breadcrumb-item"><a href="{{ route("setting.business.setting.index") }}">Business setting</a></li>
            <li class="breadcrumb-item active" aria-current="page"> Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">
        <h5 class="card-header">General information</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="card pt-3">
                        <div class="card-body">
                            @if (strlen($businessSetting->fields_with_values["logo"]) == 0)
                                <p> Not added yet.</p>
                            @endif

                            @if (strlen($businessSetting->fields_with_values["logo"]) > 0)
                                <div class="d-flex justify-content-center">
                                    <figure class="figure">
                                        <img src="{{ ($businessSetting->fields_with_values["logo"] == null) ?  asset("images/setting/default-logo.png") : asset("storage/images/setting/".$businessSetting->fields_with_values["logo"]) }}" class="img-thumbnail" alt="Business logo">
                                    </figure>
                                </div>

                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3" style="min-height: 100px !important;">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Name</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ ($businessSetting->fields_with_values["name"] == null) ? "Not added yet." : $businessSetting->fields_with_values["name"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Short name</th>
                                            <th>:</th>
                                            <td>{{ ($businessSetting->fields_with_values["short_name"] == null) ? "Not added yet." : $businessSetting->fields_with_values["short_name"] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3" style="min-height: 100px !important;">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Email</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ ($businessSetting->fields_with_values["email"] == null) ? "Not added yet." : $businessSetting->fields_with_values["email"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile no</th>
                                            <th>:</th>
                                            <td>{{ ($businessSetting->fields_with_values["mobile_no"] == null) ? "Not added yet." : $businessSetting->fields_with_values["mobile_no"] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3" style="min-height: 100px !important;">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Country</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ ($businessSetting->fields_with_values["country"] == null) ? "Not added yet." : $businessSetting->fields_with_values["country"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Country code</th>
                                            <th>:</th>
                                            <td>{{ ($businessSetting->fields_with_values["country_code"] == null) ? "Not added yet." : $businessSetting->fields_with_values["country_code"] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3" style="min-height: 100px !important;">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Currency</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ ($businessSetting->fields_with_values["currency"] == null) ? "Not added yet." : $businessSetting->fields_with_values["currency"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Currency code</th>
                                            <th>:</th>
                                            <td>{{ ( ($businessSetting->fields_with_values["currency_code"] == null) ? "Not added yet." : $businessSetting->fields_with_values["currency_code"]."".(($businessSetting->fields_with_values["currency_symbol"] == null) ? null : "(".$businessSetting->fields_with_values["currency_symbol"].")") )}} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3" style="min-height: 100px !important;">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Url</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>
                                                @if ($businessSetting->fields_with_values["url"] == null)
                                                    Not added yet.
                                                @endif
                                                @if (!($businessSetting->fields_with_values["url"] == null))
                                                    <a href="{{ $businessSetting->fields_with_values["url"] }}" class="text-decoration-none" style=" font-size:14px;">{{ $businessSetting->fields_with_values["url"] }}</a>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <div class="card pt-3" style="min-height: 100px !important;">
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="text-align: center;">Address</th>
                                            </tr>
                                            <tr>
                                                <td>{{ ($businessSetting->fields_with_values["address"] == null) ? "Not added yet." : $businessSetting->fields_with_values["address"] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card pt-3" style="min-height: 100px !important;">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="text-align: center;">Description</th>
                                    </tr>
                                    <tr>
                                        <td>{{ ($businessSetting->fields_with_values["description"] == null) ? "Not added yet." : $businessSetting->fields_with_values["description"] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card border-dark mb-3">
        <h5 class="card-header">System information</h5>
        <div class="card-body mb-1">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Created at</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($businessSetting->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($businessSetting->created_at))." at ".date('h:i:s a',strtotime($businessSetting->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($businessSetting->created_by_id == null) ? "Not added yet." : $businessSetting->createdBy->name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Update at</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($businessSetting->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($businessSetting->updated_at))." at ".date('h:i:s a',strtotime($businessSetting->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($businessSetting->updated_at == null) ? "Not updated yet." : (($businessSetting->updatedBy() == null) ? "Unknown" : $businessSetting->updatedBy()->name) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->hasUserPermission(["ACLMP01"]) == true)
        <div class="card border-dark mb-3">
            <h5 class="card-header">Activity Logs</h5>
            <div class="card-body">
                @php
                    $activitylogLimit = 3;
                @endphp

                <div class="card-body text-dark">

                    <div class="d-flex justify-content-center">
                        <p>
                            <b>Showing {{ ($activitylogLimit < $businessSetting->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $businessSetting->activityLogs()->count() }} activity log(s).</b>
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table class=" table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Event</th>
                                    <th>Causer</th>
                                    <th>Description</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($businessSetting->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
                                    <tr>
                                        <td>{{ $perIndex + 1 }}</td>
                                        <td>{{ Str::ucfirst($perActivityLogDatas->event) }}</td>
                                        <td>{{ ($perActivityLogDatas->causer == null) ? "Unknown" : $perActivityLogDatas->causer->name }}</td>
                                        <td>{{ $perActivityLogDatas->description }}</td>
                                        <td>{{ ($perActivityLogDatas->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($perActivityLogDatas->created_at))." at ".date('h:i:s a',strtotime($perActivityLogDatas->created_at)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"><b class="d-flex justify-content-center text-warning">No business setting found.</b></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <div class="btn-group" role="group">
                    @if (Auth::user()->hasUserPermission(["SMP02.03"]) == true)
                        <a href="{{ route("setting.business.setting.edit",["slug"=>$businessSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("setting.business.setting.index") }}" class="btn btn-sm btn-secondary">
                    Go to business setting
                </a>
            </div>
        </div>
    </div>
@endsection
