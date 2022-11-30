@extends('layouts.app')

@section('mainPageName')
    Setting
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("setting.index") }}">Setting</a></li>
            <li class="breadcrumb-item active" aria-current="page"> Authentication log setting index</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')
    <div class="card-body">
        <p>Authentication log setting quick view</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width: 30%;">Delete records older than</th>
                        <th style="width: 1%;">:</th>
                        <td>{{ ($authenticationLogSetting->fields_with_values["delete_records_older_than"] == null) ? "Not added yet." : $authenticationLogSetting->fields_with_values["delete_records_older_than"] }}</td>
                    </tr>
                    <tr>
                        <th>Auto delete</th>
                        <th>:</th>
                        <td>{{ ($authenticationLogSetting->fields_with_values["auto_delete"] == null) ? "Not added yet." : $authenticationLogSetting->fields_with_values["auto_delete"] }}</td>
                    </tr>
                    <tr>
                        <th>Auto delete scheduler frequency</th>
                        <th>:</th>
                        <td>{{ ($authenticationLogSetting->fields_with_values["auto_delete_scheduler_frequency"] == null) ? "Not added yet." : $authenticationLogSetting->fields_with_values["auto_delete_scheduler_frequency"] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            <div class="btn-group" role="group">

                @if (Auth::user()->hasUserPermission(["SMP04.02"]) == true)
                    <a href="{{ route("setting.authentication.log.setting.details",["slug" => $authenticationLogSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-info">Details</a>
                @endif

                @if (Auth::user()->hasUserPermission(["SMP04.03"]) == true)
                    <a href="{{ route("setting.authentication.log.setting.edit",["slug" => $authenticationLogSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("setting.index") }}" class="btn btn-sm btn-secondary">
                    Go to setting
                </a>
            </div>
        </div>
    </div>
@endsection



