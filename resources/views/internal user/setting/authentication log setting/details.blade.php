@extends('layouts.app')

@section('mainPageName')
    Setting
@endsection

@section('mainCardTitle')
    Authentication log setting details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("setting.index") }}">Setting</a></li>
            <li class="breadcrumb-item"><a href="{{ route("setting.authentication.log.setting.index") }}">Authentication log setting</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">
        <h5 class="card-header">General information</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card pt-3">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Delete records older than</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ ($authenticationLogSetting->fields_with_values["delete_records_older_than"] == null) ? "Not added yet." : $authenticationLogSetting->fields_with_values["delete_records_older_than"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Auto delete</th>
                                            <th>:</th>
                                            <td>
                                                {{ ($authenticationLogSetting->fields_with_values["auto_delete"] == null) ? "Not added yet." : $authenticationLogSetting->fields_with_values["auto_delete"] }}
                                            </td>
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
                                            <td>{{ ($authenticationLogSetting->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($authenticationLogSetting->created_at))." at ".date('h:i:s a',strtotime($authenticationLogSetting->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($authenticationLogSetting->created_by_id == null) ? "Not added yet." : $authenticationLogSetting->createdBy->name }}</td>
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
                                            <td>{{ ($authenticationLogSetting->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($authenticationLogSetting->updated_at))." at ".date('h:i:s a',strtotime($authenticationLogSetting->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($authenticationLogSetting->updated_at == null) ? "Not updated yet." : (($authenticationLogSetting->updatedBy() == null) ? "Unknown" : $authenticationLogSetting->updatedBy()->name) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $authenticationLogSetting->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $authenticationLogSetting->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($authenticationLogSetting->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
                                    <tr>
                                        <td>{{ $perIndex + 1 }}</td>
                                        <td>{{ Str::ucfirst($perActivityLogDatas->event) }}</td>
                                        <td>{{ ($perActivityLogDatas->causer == null) ? "Unknown" : $perActivityLogDatas->causer->name }}</td>
                                        <td>{{ $perActivityLogDatas->description }}</td>
                                        <td>{{ ($perActivityLogDatas->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($perActivityLogDatas->created_at))." at ".date('h:i:s a',strtotime($perActivityLogDatas->created_at)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"><b class="d-flex justify-content-center text-warning">No activity log setting found.</b></td>
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
                    @if (Auth::user()->hasUserPermission(["SMP04.03"]) == true)
                        <a href="{{ route("setting.authentication.log.setting.edit",["slug"=>$authenticationLogSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
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
                <a role="button" href="{{ route("setting.authentication.log.setting.index") }}" class="btn btn-sm btn-secondary">
                    Go to authentication log
                </a>
            </div>
        </div>
    </div>
@endsection
