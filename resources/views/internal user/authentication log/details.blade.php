@extends('layouts.app')

@section('mainPageName')
    Authentication log
@endsection

@section('mainCardTitle')
    Authentication log details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("authentication.log.index") }}">Authentication log</a></li>
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
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">User</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>
                                                @php
                                                    $authUser = App\Models\User::withTrashed()->where("id",$authenticationLog->authenticatable_id)->first();
                                                @endphp
                                                {{ ($authUser) ? ( ($authUser->id == Auth::user()->id) ? "Me": $authUser->name) : "Unknown" }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Ip address</th>
                                            <th>:</th>
                                            <td> {{ $authenticationLog->ip_address }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3" >
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Login at</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ ($authenticationLog->login_at == null) ? "Not login yet." : date('d-M-Y',strtotime($authenticationLog->login_at))." at ".date('h:i:s a',strtotime($authenticationLog->login_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Log out at</th>
                                            <th>:</th>
                                            <td>{{ ($authenticationLog->logout_at == null) ? "Not logout yet." : date('d-M-Y',strtotime($authenticationLog->logout_at))." at ".date('h:i:s a',strtotime($authenticationLog->logout_at)) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3" >
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">User agent</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ $authenticationLog->user_agent }}</td>
                                        </tr>
                                        <tr>
                                            <th>Login successful</th>
                                            <th>:</th>
                                            <td>
                                                @if ($authenticationLog->login_successful == 1)
                                                    <span class="badge text-bg-success p-2">Yes</span>
                                                @endif

                                                @if ($authenticationLog->login_successful == 0)
                                                    <span class="badge text-bg-success p-2">No</span>
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
                    <div class="card pt-3">
                        <div class="card-body">
                            <p class="d-flex justify-content-center">
                                <b>Location</b>
                            </p>

                            {{ $authenticationLog->description }}

                            @if ($authenticationLog->location == null)
                                <p>No location found</p>
                            @endif

                            @if (!($authenticationLog->location == null))
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach ($authenticationLog->location as $perLocationKey => $perLocationValue )
                                                <tr>
                                                    <th style="width: auto;">{{ str_replace("_"," ",Str::ucfirst($perLocationKey)) }}</th>
                                                    <th style="width: 1%;">:</th>
                                                    <td>{{ ((Str::lower($perLocationKey) == "cached") || (Str::lower($perLocationKey) == "default")) ? ( ($perLocationValue == false) ? "False" : "True") : $perLocationValue }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if (Auth::user()->hasUserPermission(["AULMP03"]) == true)
        <div class="card border-dark mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div class="btn-group" role="group" >
                        <button type="button" class="btn btn btn-danger" data-bs-toggle="modal" data-bs-target="#activityLogDeleteConfirmationModal">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="activityLogDeleteConfirmationModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $authenticationLog->log_name }} delete confirmation modal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            If you delete this you can not recover it. Are you sure you want to delete this?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <form action="{{ route("activity.log.delete",["id" => $authenticationLog->id]) }}" method="POST">
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="btn btn-success">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("activity.log.index") }}" class="btn btn-sm btn-secondary">
                    Go to activity log
                </a>
            </div>
        </div>
    </div>
@endsection
