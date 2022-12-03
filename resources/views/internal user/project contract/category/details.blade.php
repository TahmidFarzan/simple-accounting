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
            <li class="breadcrumb-item"><a href="{{ route("project.contract.category.index") }}">Category</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-2">
        <h5 class="card-header">General information</h5>
        <div class="card-body text-dark mb-2">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <th style="width: 25%;">Name</th>
                            <th style="width: 1%;">:</th>
                            <td>{{ $projectContractCategory->name }}</td>
                        </tr>
                        <tr>
                            <th>Code</th>
                            <th>:</th>
                            <td>{{ $projectContractCategory->code }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <th>:</th>
                            <td>
                                @if ($projectContractCategory->deleted_at == null)
                                    <span class="badge bg-success p-2 text-bold text-lg">Active</span>
                                @endif

                                @if (!($projectContractCategory->deleted_at == null))
                                    <span class="badge bg-warning p-2 text-bold text-lg">Trash</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-dark mb-2">
        <h5 class="card-header"> Description</h5>
        <div class="card-body text-dark">
            <div class=" d-flex justify-content-center">
                {{ ($projectContractCategory->description == null) ? "Not added." : $projectContractCategory->description }}
            </div>
        </div>
    </div>

    <div class="card border-dark mb-2">
        <h5 class="card-header"> Tree information</h5>
        <div class="card-body text-dark">
            <div class=" d-flex justify-content-center">
                <x-project_contract.category.tree.categories :categories="$projectContractCategoryTree"/>
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
                                            <td>{{ ($projectContractCategory->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($projectContractCategory->created_at))." at ".date('h:i:s a',strtotime($projectContractCategory->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContractCategory->created_by_id == null) ? "Not added yet." : $projectContractCategory->createdBy->name }}</td>
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
                                            <td>{{ ($projectContractCategory->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($projectContractCategory->updated_at))." at ".date('h:i:s a',strtotime($projectContractCategory->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContractCategory->updated_at == null) ? "Not updated yet." : (($projectContractCategory->updatedBy() == null) ? "Unknown" : $projectContractCategory->updatedBy()->name) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!($projectContractCategory->deleted_at == null))
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;">Deleted at</th>
                                                <th style="width: 1%;">:</th>
                                                <td>{{ ($projectContractCategory->deleted_at == null) ? "Not added yet." : date('d-M-Y',strtotime($projectContractCategory->deleted_at))." at ".date('h:i:s a',strtotime($projectContractCategory->deleted_at)) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $projectContractCategory->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $projectContractCategory->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($projectContractCategory->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
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
                    @if (Auth::user()->hasUserPermission(["PCCAMP04"]) == true)
                        <a href="{{ route("project.contract.category.edit",["slug"=>$projectContractCategory->slug]) }}" class="btn btn-primary">Edit</a>
                    @endif

                    @if (!($projectContractCategory->deleted_at == null) && (Auth::user()->hasUserPermission(["PCCAMP05"]) == true))
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#restoreConfirmationModal">
                            Restore
                        </button>
                    @endif

                    @if (($projectContractCategory->deleted_at == null) && (Auth::user()->hasUserPermission(["PCCAMP06"]) == true))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#trashConfirmationModal">
                            Trash
                        </button>
                    @endif
                </div>
            </div>

            @if (!($projectContractCategory->deleted_at == null) && (Auth::user()->hasUserPermission(["PCCAMP05"]) == true))
                <div class="modal fade" id="restoreConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $projectContractCategory->name }} restore confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>Contract category will show in the tree.</li>
                                        <li>All the sub contract category(ies) will show in the tree.</li>
                                        @foreach ($projectContractCategory->dependencyNeedToRestoreRecordsInfo() as $perDependency)
                                            <li>{{ $perDependency }}</li>
                                        @endforeach
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("project.contract.category.restore",["slug" => $projectContractCategory->slug]) }}" method="POST">
                                    @csrf
                                    @method("PATCH")
                                    <button type="submit" class="btn btn-sm btn-success">Yes,Restore</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (($projectContractCategory->deleted_at == null) && (Auth::user()->hasUserPermission(["PCCAMP06"]) == true))
                <div class="modal fade" id="trashConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $projectContractCategory->name }} trash confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>Contract category will not show in the tree except edit.</li>
                                        <li>All the sub contract category(ies) will not show in the tree except edit.</li>
                                        @foreach ($projectContractCategory->dependencyNeedToTrashRecordsInfo() as $perDependency)
                                            <li>{{ $perDependency }}</li>
                                        @endforeach
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("project.contract.category.trash",["slug" => $projectContractCategory->slug]) }}" method="POST">
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
                <a role="button" href="{{ route("project.contract.category.index") }}" class="btn btn-sm btn-secondary">
                    Go to contract category
                </a>
            </div>
        </div>
    </div>
@endsection
