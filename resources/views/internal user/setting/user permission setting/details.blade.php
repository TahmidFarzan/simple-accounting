@extends('layouts.app')

@section('mainPageName')
    Supplier
@endsection

@section('mainCardTitle')
    Details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("setting.index") }}">Setting</a></li>
            <li class="breadcrumb-item"><a href="{{ route("setting.user.permission.setting.index") }}">User permission setting</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-2">
        <h5 class="card-header">General information</h5>
        <div class="card-body text-dark mb-2">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Name</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $userPermission->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type</th>
                                            <th>:</th>
                                            <td>{{ Str::ucfirst(Str::lower(preg_replace("/([a-z])([A-Z])/", "$1 $2", $userPermission->type))) }}</td>
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
                                            <th>Description</th>
                                            <th>:</th>
                                            <td>{{ ($userPermission->description == null) ? "Not added yet." : $userPermission->description }}</td>
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
        <div class="card-body">
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
                                            <td>{{ ($userPermission->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($userPermission->created_at))." at ".date('h:i:s a',strtotime($userPermission->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>System</td>
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

