@extends('layouts.app')

@section('mainPageName')
    User
@endsection

@section('mainCardTitle')
    Details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("user.index") }}">User</a></li>
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
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>User role</th>
                                            <th>:</th>
                                            <td>{{ $user->user_role }}</td>
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
                                            <th style="width: 35%;">Mobile no</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $user->mobile_no }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <th>:</th>
                                            <td>{{ ($user->email == null) ? "Not added yet." : $user->email }}</td>
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
                                            <th style="width: 35%;">Default password</th>
                                            <th style="width: 1%;">:</th>
                                            <td>
                                                @if ($user->default_password == 1)
                                                    <span class="badge text-bg-warning p-2"><i class="fa-sharp fa-solid fa-check"></i></span>
                                                @endif

                                                @if ($user->default_password == 0)
                                                    <span class="badge text-bg-success p-2"><i class="fa-sharp fa-solid fa-xmark"></i></span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <th>:</th>
                                            <td>
                                                @if ($user->deleted_at == null)
                                                    <span class="badge bg-success p-2 text-bold text-lg">Active</span>
                                                @endif

                                                @if (!($user->deleted_at == null))
                                                    <span class="badge bg-warning p-2 text-bold text-lg">Trash</span>
                                                @endif
                                            </td>
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
                                            <th style="width: 35%;">Email verified</th>
                                            <th style="width: 1%;">:</th>
                                            <td>
                                                @if ($user->email_verified_at == null)
                                                    <span class="badge text-bg-warning p-2"><i class="fa-sharp fa-solid fa-xmark"></i></span>
                                                @endif

                                                @if (!($user->email_verified_at == null))
                                                    <span class="badge text-bg-success p-2"><i class="fa-sharp fa-solid fa-check"></i></span>
                                                @endif

                                                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                                    @csrf
                                                    <button type="submit" class="badge p-2 btn btn-primary align-baseline">{{ ($user->email_verified_at == null) ? "Verify" : "Reverify"}} </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th style="width: 35%;">Email verified at</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($user->email_verified_at == null) ? "Not verified yet." : date('d-M-Y',strtotime($user->email_verified_at))." at ".date('h:i:s a',strtotime($user->email_verified_at)) }}</td>
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
                                            <td>{{ ($user->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($user->created_at))." at ".date('h:i:s a',strtotime($user->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($user->created_by_id == null) ? "Not added yet." : $user->createdBy->name }}</td>
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
                                            <td>{{ ($user->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($user->updated_at))." at ".date('h:i:s a',strtotime($user->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($user->updated_at == null) ? "Not updated yet." : (($user->updatedBy()==null) ? "Unknown" : $user->updatedBy()->name) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!($user->deleted_at == null))
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;">Deleted at</th>
                                                <th style="width: 1%;">:</th>
                                                <td>{{ ($user->deleted_at == null) ? "Not added yet." : date('d-M-Y',strtotime($user->deleted_at))." at ".date('h:i:s a',strtotime($user->deleted_at)) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
                            <b>Showing {{ ($activitylogLimit < $user->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $user->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($user->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
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

            @php
                $restoreAableUser = false;
                if(($user->user_role == "Owner") && (Auth::user()->hasUserPermission(["UMP09"]) == true)){
                    $restoreAableUser = true;
                }

                if(($user->user_role == "Subordinate") && (Auth::user()->hasUserPermission(["UMP10"]) == true)){
                    $restoreAableUser = true;
                }
            @endphp

            @php
                $trashAableUser = false;
                if(($user->user_role == "Owner") && (Auth::user()->hasUserPermission(["UMP07"]) == true)){
                    $trashAableUser = true;
                }

                if(($user->user_role == "Subordinate") && (Auth::user()->hasUserPermission(["UMP08"]) == true)){
                    $trashAableUser = true;
                }
            @endphp

            <div class="d-flex justify-content-center">
                <div class="btn-group" role="group">
                    @if ((Auth::user()->hasUserPermission(["UMP05","UMP06"]) == true) && !(Auth::user()->id == $user->id))
                        <a href="{{ route("user.edit",["slug" => $user->slug]) }}" class="btn btn-primary">Edit</a>
                    @endif

                    @if (( $restoreAableUser == true) && !(Auth::user()->id == $user->id))
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#userRestoreConfirmationModal">
                            Restore
                        </button>
                    @endif

                    @if (($trashAableUser == true) && !(Auth::user()->id == $user->id))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#userTrashConfirmationModal">
                            Trash
                        </button>
                    @endif
                </div>
            </div>

            @if (( $restoreAableUser == true) && !(Auth::user()->id == $user->id))
                <div class="modal fade" id="userRestoreConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $user->name }} restore confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>User will show.</li>
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("user.restore",["slug" => $user->slug]) }}" method="POST">
                                    @csrf
                                    @method("PATCH")
                                    <button type="submit" class="btn btn-sm btn-success">Yes,Restore</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (($trashAableUser == true) && !(Auth::user()->id == $user->id))
                <div class="modal fade" id="userTrashConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $user->name }} trash confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>User will not show.</li>
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("user.trash",["slug" => $user->slug]) }}" method="POST">
                                    @csrf
                                    @method("DELETE")
                                    <button type="submit" class="btn btn-sm btn-success">Yes,Trash</button>
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
                <a role="button" href="{{ route("user.index") }}" class="btn btn-sm btn-secondary">
                    Go to user
                </a>
            </div>
        </div>
    </div>
@endsection
