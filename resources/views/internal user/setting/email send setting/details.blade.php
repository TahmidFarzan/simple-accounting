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
            <li class="breadcrumb-item"><a href="{{ route("setting.email.send.setting.index") }}">Email send setting</a></li>
            <li class="breadcrumb-item active" aria-current="page"> Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">
        <h5 class="card-header">General information</h5>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card pt-3" style="min-height: 100px !important;">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>From </th>
                                            <th>:</th>
                                            <td>{{ $emailSendSetting->fields_with_values["from"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>To </th>
                                            <th>:</th>
                                            <td>{{ $emailSendSetting->fields_with_values["to"] }}</td>
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
                                            <th>CC </th>
                                            <th>:</th>
                                            <td>{{ $emailSendSetting->fields_with_values["cc"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reply </th>
                                            <th>:</th>
                                            <td>{{ $emailSendSetting->fields_with_values["reply"] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($emailSendSetting->fields_with_values["module"] as $perArrayIndex => $perArrayValue)
                    <div class="col-md-6 mb-2">
                        <div class="card pt-3" style="min-height: 100px !important;">

                            <div class="d-flex justify-content-center">
                                <b>
                                    <p>{{ Str::ucfirst(Str::lower(preg_replace("/([a-z])([A-Z])/", "$1 $2", $perArrayIndex))) }}</p>
                                </b>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                @foreach ($perArrayValue as $perIndex => $perValue )
                                                    <tr>
                                                        <th>{{ str::ucfirst(str_replace("_"," ",$perIndex)) }}</th>
                                                        <th>:</th>
                                                        <td>{{ $perValue }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
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
                                            <td>{{ ($emailSendSetting->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($emailSendSetting->created_at))." at ".date('h:i:s a',strtotime($emailSendSetting->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($emailSendSetting->created_by_id == null) ? "Not added yet." : $emailSendSetting->createdBy->name }}</td>
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
                                            <td>{{ ($emailSendSetting->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($emailSendSetting->updated_at))." at ".date('h:i:s a',strtotime($emailSendSetting->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($emailSendSetting->updated_at == null) ? "Not updated yet." : (($emailSendSetting->updatedBy() == null) ? "Unknown" : $emailSendSetting->updatedBy()->name) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $emailSendSetting->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $emailSendSetting->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($emailSendSetting->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
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
                    @if (Auth::user()->hasUserPermission(["SMP05.03"]) == true)
                        <a href="{{ route("setting.email.send.setting.edit",["slug"=>$emailSendSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
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
                <a role="button" href="{{ route("setting.email.send.setting.index") }}" class="btn btn-sm btn-secondary">
                    Go to email send setting
                </a>
            </div>
        </div>
    </div>
@endsection
