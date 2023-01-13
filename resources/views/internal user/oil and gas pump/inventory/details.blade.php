@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump product
@endsection

@section('mainCardTitle')
    Details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $inventory->oagpProduct->oilAndGasPump->slug]) }}">{{ $inventory->oagpProduct->oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.inventory.index",["oagpSlug" => $inventory->oagpProduct->oilAndGasPump->slug]) }}">Inventory</a></li>
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
                            <td>{{ $inventory->oagpProduct->name }}</td>
                        </tr>
                        <tr>
                            <th style="width: 25%;">Type</th>
                            <th style="width: 1%;">:</th>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-body text-dark mb-2">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="d-flex justify-content-center mt-2">
                            <h5>Current</h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Count</th>
                                            <th style="width: 1%;">:</th>
                                            <td>
                                                {{ $inventory->count }}
                                                @if ( $inventory->oagpProduct->type == "Oil")
                                                    {{ $setting["oagpSetting"]["oil_unit"] }}
                                                @endif

                                                @if ( $inventory->oagpProduct->type == "Gas")
                                                    {{ $setting["oagpSetting"]["gas_unit"] }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Sell price</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $inventory->sell_price }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Purchase price</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $inventory->purchase_price }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="d-flex justify-content-center mt-2">
                            <h5>Previous</h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Count</th>
                                            <th style="width: 1%;">:</th>
                                            <td>
                                                {{ $inventory->previous_count }}
                                                @if ( $inventory->oagpProduct->type == "Oil")
                                                    {{ $setting["oagpSetting"]["oil_unit"] }}
                                                @endif

                                                @if ( $inventory->oagpProduct->type == "Gas")
                                                    {{ $setting["oagpSetting"]["gas_unit"] }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Sell price</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $inventory->previous_sell_price }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Purchase price</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $inventory->previous_purchase_price }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
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
                                            <td>{{ ($inventory->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($inventory->created_at))." at ".date('h:i:s a',strtotime($inventory->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($inventory->created_by_id == null) ? "Not added yet." : $inventory->createdBy->name }}</td>
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
                                            <td>{{ ($inventory->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($inventory->updated_at))." at ".date('h:i:s a',strtotime($inventory->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($inventory->updated_at == null) ? "Not updated yet." : (($inventory->updatedBy() == null) ? "Unknown" : $inventory->updatedBy()->name) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $inventory->activityLogs()->count() ) ? "last ".$inventory : "All" }} out of {{ $inventory->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($inventory->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
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
                    @if (($inventory->deleted_at == null) && (Auth::user()->hasUserPermission(["OAGPIMP04"]) == true))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                            Delete
                        </button>
                    @endif
                </div>
            </div>

            @if (($inventory->deleted_at == null) && (Auth::user()->hasUserPermission(["OAGPIMP04"]) == true))
                <div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $inventory->name }} delete confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>You can not recover this.</li>
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("oil.and.gas.pump.inventory.delete",["oagpSlug" => $inventory->oagpProduct->oilAndGasPump->slug,"inSlug"=>$inventory->slug]) }}" method="POST">
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
                <a role="button" href="{{ route("oil.and.gas.pump.inventory.index",["oagpSlug" => $inventory->oagpProduct->oilAndGasPump->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to inventory
                </a>
            </div>
        </div>
    </div>
@endsection
