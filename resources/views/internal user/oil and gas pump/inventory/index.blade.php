@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $oilAndGasPump->slug]) }}">{{ $oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item">Inventory</li>
            <li class="breadcrumb-item active" aria-current="page">Index</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')
    <div class="card border-dark mb-2">
        <div class="card-body text-dark mt-0">
            <div class="row mb-2">
                <p>
                    @if (Auth::user()->hasUserPermission(["OAGPIMP02"]) == true)
                        <a href="{{ route("oil.and.gas.pump.inventory.add",["oagpSlug" => $oilAndGasPump->slug]) }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create product</a>
                    @endif
                </p>
            </div>

            <div class="row mb-2" id="extraErrorMessageDiv" style="display: none;"></div>

            <div class="row">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Sell price</th>
                                    <th>Purchase price</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inventories as $perInventoryIndex => $perInventory)
                                    <tr>
                                        <td>{{ $perInventoryIndex + 1 }}</td>
                                        <td>{{ $perInventory->oagpProduct->name }}</td>
                                        <td>{{ $perInventory->quantity }}</td>
                                        <td>{{ $perInventory->sell_price }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        <td>{{ $perInventory->purchase_price }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        <td>
                                            @if (Auth::user()->hasUserPermission(["OAGPIMP03"]) == true)
                                                <a href="{{ route("oil.and.gas.pump.inventory.details",["oagpSlug" => $oilAndGasPump->slug,"inSlug" => $perInventory->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                            @endif

                                            @if (Auth::user()->hasUserPermission(["OAGPIMP04"]) == true)
                                                <button type="button" class="btn btn-sm btn-danger m-1" data-bs-toggle="modal" data-bs-target="#{{$perInventory->slug}}DeleteConfirmationModal">
                                                    Delete
                                                </button>

                                                <div class="modal fade" id="{{$perInventory->slug}}DeleteConfirmationModal" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5">{{ $perInventory->name }} delete confirmation.</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>
                                                                    <ul>
                                                                        <li>You can not recover it.</li>
                                                                    </ul>
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                <form action="{{ route("oil.and.gas.pump.inventory.delete",["oagpSlug" => $oilAndGasPump->slug,"inSlug" => $perInventory->slug]) }}" method="POST">
                                                                    @csrf
                                                                    @method("DELETE")
                                                                    <button type="submit" class="btn btn-sm btn-success">Yes,Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <b class="d-flex justify-content-center text-warning">No product found.</b>
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
@endsection


