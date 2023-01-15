@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump
@endsection

@section('mainCardTitle')
    Details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
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
                            <td>{{ $oilAndGasPump->name }}</td>
                        </tr>
                        <tr>
                            <th style="width: 25%;">Code</th>
                            <th style="width: 1%;">:</th>
                            <td>{{ $oilAndGasPump->code }}</td>
                        </tr>
                    </tbody>
                </table>
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
                                {{ ($oilAndGasPump->description == null) ? "Not added." : $oilAndGasPump->description }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <b class="d-flex justify-content-center mb-1">
                                Note
                            </b>

                            <ul>
                                @forelse ($oilAndGasPump->note as  $perNote )
                                    <li> {{ $perNote }}</li>
                                @empty
                                    <li> No note added.</li>
                                @endforelse
                            </ul>
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
                                            <td>{{ ($oilAndGasPump->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($oilAndGasPump->created_at))." at ".date('h:i:s a',strtotime($oilAndGasPump->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($oilAndGasPump->created_by_id == null) ? "Not added yet." : $oilAndGasPump->createdBy->name }}</td>
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
                                            <td>{{ ($oilAndGasPump->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($oilAndGasPump->updated_at))." at ".date('h:i:s a',strtotime($oilAndGasPump->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($oilAndGasPump->updated_at == null) ? "Not updated yet." : (($oilAndGasPump->updatedBy() == null) ? "Unknown" : $oilAndGasPump->updatedBy()->name) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $oilAndGasPump->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $oilAndGasPump->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($oilAndGasPump->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
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
                    @if (Auth::user()->hasUserPermission(["OAGPMP04"]) == true)
                        <a href="{{ route("oil.and.gas.pump.edit",["slug"=>$oilAndGasPump->slug]) }}" class="btn btn-primary">Edit</a>
                    @endif

                    @if (($oilAndGasPump->deleted_at == null) && (Auth::user()->hasUserPermission(["OAGPMP05"]) == true))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                            Delete
                        </button>
                    @endif

                    @if (Auth::user()->hasUserPermission(["OAGPPMP01"]) == true)
                        <a href="{{ route("oil.and.gas.pump.product.index",["oagpSlug" => $oilAndGasPump->slug]) }}" class="btn btn-primary">Products</a>
                    @endif

                    @if (Auth::user()->hasUserPermission(["OAGPIMP01"]) == true)
                        <a href="{{ route("oil.and.gas.pump.inventory.index",["oagpSlug" => $oilAndGasPump->slug]) }}" class="btn btn-secondary">Inventory</a>
                    @endif

                    @if (Auth::user()->hasUserPermission(["OAGPSMP01"]) == true)
                        <a href="{{ route("oil.and.gas.pump.supplier.index",["oagpSlug" => $oilAndGasPump->slug]) }}" class="btn btn-dark">Supplier</a>
                    @endif
                </div>
            </div>


            @if (($oilAndGasPump->deleted_at == null) && (Auth::user()->hasUserPermission(["OAGPMP05"]) == true))
                <div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $oilAndGasPump->name }} delete confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>Oil and pump will not show dependency.</li>
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("oil.and.gas.pump.delete",["slug" => $oilAndGasPump->slug]) }}" method="POST">
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
                <a role="button" href="{{ route("oil.and.gas.pump.index") }}" class="btn btn-sm btn-secondary">
                    Go to oil and gas pump
                </a>
            </div>
        </div>
    </div>
@endsection
