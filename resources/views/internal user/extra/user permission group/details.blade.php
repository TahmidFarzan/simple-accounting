@extends('layouts.app')

@section('mainPageName')
    User permission group
@endsection

@section('mainCardTitle')
    Details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("user.permission.group.index") }}">User permission group</a></li>
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
                                            <td>{{ $userPermissionGroup->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Code</th>
                                            <th>:</th>
                                            <td>{{ $userPermissionGroup->code }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-center">
                                <p>
                                    <b>Showing {{ (3 < $userPermissionGroup->userPermissions->count() ) ? "top 3" : "All" }} out of {{ $userPermissionGroup->userPermissions->count() }} permission(s).</b>
                                </p>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($userPermissionGroup->userPermissions()->orderBy("name","asc")->take(3)->get() as $perUserPermissionIndex => $perUserPermission)
                                            <tr>
                                                <td>{{ $perUserPermissionIndex + 1 }}</td>
                                                <td>{{ $perUserPermission->name }}</td>
                                                <td>{{  Str::ucfirst(Str::lower(preg_replace("/([a-z])([A-Z])/", "$1 $2", $perUserPermission->type))) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3">
                                                    <b class="d-flex justify-content-center text-warning">No user permission found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
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
                                            <td>{{ ($userPermissionGroup->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($userPermissionGroup->created_at))." at ".date('h:i:s a',strtotime($userPermissionGroup->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($userPermissionGroup->created_by_id == null) ? "Not added yet." : $userPermissionGroup->createdBy->name }}</td>
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
                                            <th style="width: 25%;">Updated at</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($userPermissionGroup->updated_at == null) ? "Not updated at yet." : date('d-M-Y',strtotime($userPermissionGroup->updated_at))." at ".date('h:i:s a',strtotime($userPermissionGroup->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Updated by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($userPermissionGroup->updated_at == null) ? "Not updated yet." : (($userPermissionGroup->updatedBy() == null) ? "Unknown" : $userPermissionGroup->updatedBy()->name) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $userPermissionGroup->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $userPermissionGroup->activityLogs()->count() }} activity log(s).</b>
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped">
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
                                @forelse ($userPermissionGroup->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
                                    <tr>
                                        <td>{{ $perIndex + 1 }}</td>
                                        <td>{{ Str::ucfirst($perActivityLogDatas->event) }}</td>
                                        <td>{{ ($perActivityLogDatas->causer == null) ? "Unkbown" : $perActivityLogDatas->causer->name }}</td>
                                        <td>{{ $perActivityLogDatas->description }}</td>
                                        <td>{{ ($perActivityLogDatas->created_at==null) ? "Not added yet." : date('d-M-Y',strtotime($perActivityLogDatas->created_at))." at ".date('h:i:s a',strtotime($perActivityLogDatas->created_at)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <b class="d-flex justify-content-center text-warning">No activity found.</b>
                                        </td>
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
                    @if (Auth::user()->hasUserPermission(["UPGMP04"]) == true)
                        <a href="{{ route("user.permission.group.edit",["slug" => $userPermissionGroup->slug]) }}" class="btn btn-primary">Edit</a>
                    @endif

                    @if (Auth::user()->hasUserPermission(["UPGMP05"]) == true)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete{{$userPermissionGroup->slug}}ConfirmationModal">
                            Delete
                        </button>
                    @endif
                </div>
            </div>

            @if (Auth::user()->hasUserPermission(["UPGMP05"]) == true)
                <div class="modal fade" id="delete{{$userPermissionGroup->slug}}ConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $userPermissionGroup->name }} delete confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>This record will be deleted.</li>
                                        <li>Recovery of this record is not possible.</li>
                                        <li>Dependency relatioon between user permissions which and this user permission group will be deleted.</li>
                                        <li> Recovery of dependency relatioon between user permissions which and this user permission group is not possible.</li>
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("user.permission.group.delete",["slug" => $userPermissionGroup->slug]) }}" method="POST">
                                    @csrf
                                    @method("DELETE")
                                    <button type="submit" class="btn btn-sm btn-success">Yes,Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("user.permission.group.index") }}" class="btn btn-sm btn-secondary">
                    Go to user permission group
                </a>
            </div>
        </div>
    </div>
@endsection

