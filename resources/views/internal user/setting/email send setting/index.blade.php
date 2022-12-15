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
            <li class="breadcrumb-item active" aria-current="page"> Email send setting index</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')
    <div class="card-body">
        <p>Email send setting quick view</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
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
        <div class="d-flex justify-content-center">
            <div class="btn-group" role="group">

                @if (Auth::user()->hasUserPermission(["SMP05.02"]) == true)
                    <a href="{{ route("setting.email.send.setting.details",["slug" => $emailSendSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-info">Details</a>
                @endif

                @if (Auth::user()->hasUserPermission(["SMP05.03"]) == true)
                    <a href="{{ route("setting.email.send.setting.edit",["slug" => $emailSendSetting->slug]) }}" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
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


