@extends('layouts.app')

@section('mainPageName')
    Project contract
@endsection

@section('mainCardTitle')
    Details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item">Project contract</li>
            <li class="breadcrumb-item"><a href="{{ route("project.contract.client.index") }}">Client</a></li>
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
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Name</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $projectContractClient->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <th>:</th>
                                            <td>
                                                @if ($projectContractClient->deleted_at == null)
                                                    <span class="badge bg-success p-2 text-bold text-lg">Active</span>
                                                @endif

                                                @if (!($projectContractClient->deleted_at == null))
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
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Email</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContractClient->email == null) ? "Not added" :  $projectContractClient->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile no</th>
                                            <th>:</th>
                                            <td>
                                                {{ ($projectContractClient->mobile_no == null) ? "Not added" :  $projectContractClient->mobile_no }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Gender</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContractClient->gender == null) ? "Not added" :  $projectContractClient->gender }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <th>:</th>
                                            <td>
                                                {{ ($projectContractClient->address == null) ? "Not added" :  $projectContractClient->address }}
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

        <div class="card-body text-dark mb-2">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <b class="d-flex justify-content-center mb-1">
                                Description
                            </b>
                            <p>
                                {{ ($projectContractClient->description == null) ? "Not added." : $projectContractClient->description }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <b class="d-flex justify-content-center mb-1">
                                Note
                            </b>
                            <p>
                                {{ ($projectContractClient->note == null) ? "Not added." : $projectContractClient->note }}
                            </p>
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
                                            <td>{{ ($projectContractClient->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($projectContractClient->created_at))." at ".date('h:i:s a',strtotime($projectContractClient->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContractClient->created_by_id == null) ? "Not added yet." : $projectContractClient->createdBy->name }}</td>
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
                                            <td>{{ ($projectContractClient->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($projectContractClient->updated_at))." at ".date('h:i:s a',strtotime($projectContractClient->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContractClient->updated_at == null) ? "Not updated yet." : (($projectContractClient->updatedBy() == null) ? "Unknown" : $projectContractClient->updatedBy()->name) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!($projectContractClient->deleted_at == null))
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;">Deleted at</th>
                                                <th style="width: 1%;">:</th>
                                                <td>{{ ($projectContractClient->deleted_at == null) ? "Not added yet." : date('d-M-Y',strtotime($projectContractClient->deleted_at))." at ".date('h:i:s a',strtotime($projectContractClient->deleted_at)) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $projectContractClient->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $projectContractClient->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($projectContractClient->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
                                    <tr>
                                        <td>{{ $perIndex + 1 }}</td>
                                        <td>{{ Str::ucfirst($perActivityLogDatas->event) }}</td>
                                        <td>{{ ($perActivityLogDatas->causer == null) ? "Unknown": $perActivityLogDatas->causer->name }}</td>
                                        <td>{{ $perActivityLogDatas->description }}</td>
                                        <td>{{ ($perActivityLogDatas->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($perActivityLogDatas->created_at))." at ".date('h:i:s a',strtotime($perActivityLogDatas->created_at)) }}</td>
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
                    @if (Auth::user()->hasUserPermission(["PCCLMP04"]) == true)
                        <a href="{{ route("project.contract.client.edit",["slug"=>$projectContractClient->slug]) }}" class="btn btn-primary">Edit</a>
                    @endif

                    @if (!($projectContractClient->deleted_at == null) && (Auth::user()->hasUserPermission(["PCCLMP05"]) == true))
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#restoreConfirmationModal">
                            Restore
                        </button>
                    @endif

                    @if (($projectContractClient->deleted_at == null) && (Auth::user()->hasUserPermission(["PCCLMP06"]) == true))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#trashConfirmationModal">
                            Trash
                        </button>
                    @endif
                </div>
            </div>

            @if (!($projectContractClient->deleted_at == null) && (Auth::user()->hasUserPermission(["PCCLMP05"]) == true))
                <div class="modal fade" id="restoreConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $projectContractClient->name }} restore confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <p>
                                        <ul>
                                            <li>Client will show dependency.</li>
                                        </ul>
                                    </p>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("project.contract.client.restore",["slug" => $projectContractClient->slug]) }}" method="POST">
                                    @csrf
                                    @method("PATCH")
                                    <button type="submit" class="btn btn-sm btn-success">Yes,Restore</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (($projectContractClient->deleted_at == null) && (Auth::user()->hasUserPermission(["PCCLMP06"]) == true))
                <div class="modal fade" id="trashConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $projectContractClient->name }} trash confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>Client will not show dependency.</li>
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("project.contract.client.trash",["slug" => $projectContractClient->slug]) }}" method="POST">
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
                <a role="button" href="{{ route("project.contract.client.index") }}" class="btn btn-sm btn-secondary">
                    Go to client
                </a>
            </div>
        </div>
    </div>
@endsection
