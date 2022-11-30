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
            <li class="breadcrumb-item active" aria-current="page">Setting</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')
    <div class="card border-dark mb-2">
        <div class="d-flex justify-content-center mt-3">
            <p>
                <b> Basic Setting</b>
            </p>
        </div>
        <div class="card-body text-dark mt-0">
            <div class="row">
                @if (Auth::user()->hasUserPermission(["SMP02.01"]) == true)
                    <div class="col-md-3 mb-2">
                        <a href="{{ route("setting.business.setting.index") }}" class="btn btn-link text-decoration-none">
                            <img src="{{ asset("images/setting/business-setting-logo.png") }}" class="img-thumbnail" alt="Business setting">
                            <p>
                                Business setting
                            </p>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card border-dark mb-2">
        <div class="d-flex justify-content-center mt-3">
            <p>
                <b> Log Setting</b>
            </p>
        </div>
        <div class="card-body text-dark mt-0">
            <div class="row">
                @if (Auth::user()->hasUserPermission(["SMP03.01"]) == true)
                    <div class="col-md-6 mb-2">
                        <a href="{{ route("setting.activity.log.setting.index") }}" class="btn btn-link text-decoration-none">
                            <img src="{{ asset("images/setting/activity-log-setting-logo.png") }}" class="img-thumbnail" alt="Activity log setting logo">
                            <p>
                                Activity log setting
                            </p>
                        </a>
                    </div>
                @endif

                @if (Auth::user()->hasUserPermission(["SMP04.01"]) == true)
                    <div class="col-md-6">
                        <a href="{{ route("setting.authentication.log.setting.index") }}" class="btn btn-link text-decoration-none">
                            <img src="{{ asset("images/setting/authentication-log-setting-logo.png") }}" class="img-thumbnail" alt="Authentication log setting logo">
                            <p>
                                Authentication log setting
                            </p>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card border-dark mb-2">
        <div class="d-flex justify-content-center mt-3">
            <p>
                <b> User Setting</b>
            </p>
        </div>
        <div class="card-body text-dark mt-0">
            <div class="row">
                @if (Auth::user()->hasUserPermission(["SMP05.01"]) == true)
                    <div class="col-md-6 mb-2">
                        <a href="{{ route("setting.user.permission.setting.index") }}" class="btn btn-link text-decoration-none">
                            <img src="{{ asset("images/setting/user-permission-setting-logo.png") }}" class="img-thumbnail" alt="Activity log setting logo">
                            <p>
                                User permission setting
                            </p>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <h5 class="card-title">Dark card title 2</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
        </div>
    </div>
@endsection
