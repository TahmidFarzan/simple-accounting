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
            <li class="breadcrumb-item active" aria-current="page"> Business setting index</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')
    <div class="card-body">
        <p>Business setting quick view</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width: 30%;">Name</th>
                        <th style="width: 1%;">:</th>
                        <td>{{ ($businessSetting->fields_with_values["name"] == null) ? "Not added yet." : $businessSetting->fields_with_values["name"] }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <th>:</th>
                        <td>{{ ($businessSetting->fields_with_values["email"] == null) ? "Not added yet." : $businessSetting->fields_with_values["email"] }}</td>
                    </tr>
                    <tr>
                        <th style="width: 30%;">Address</th>
                        <th style="width: 1%;">:</th>
                        <td>{{ ($businessSetting->fields_with_values["address"] == null) ? "Not added yet." : $businessSetting->fields_with_values["address"] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            <div class="btn-group" role="group">

                @if (Auth::user()->hasUserPermission(["SMP02.02"]) == true)
                    <a href="{{ route("setting.business.setting.details",["slug" => $businessSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-info">Details</a>
                @endif

                @if (Auth::user()->hasUserPermission(["SMP02.03"]) == true)
                    <a href="{{ route("setting.business.setting.edit",["slug" => $businessSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
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


