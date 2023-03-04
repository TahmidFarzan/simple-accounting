@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump sell
@endsection

@section('mainCardTitle')
    Details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $oilAndGasPumpSell->oilAndGasPump->slug]) }}">{{ $oilAndGasPumpSell->oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.sell.index",["oagpSlug" => $oilAndGasPumpSell->oilAndGasPump->slug]) }}">Sell</a></li>
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
                                            <th style="width: 25%;">Customer</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $oilAndGasPumpSell->customer }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Customer info</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $oilAndGasPumpSell->customer_info }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <th>:</th>
                                            <td>
                                                {{ date('d-M-Y', strtotime($oilAndGasPumpSell->date)) }}
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
                                            <th style="width: 25%;">Invoice</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $oilAndGasPumpSell->invoice }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <th>:</th>
                                            <td>
                                                {{ date('d-M-Y', strtotime($oilAndGasPumpSell->date)) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Name</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $oilAndGasPumpSell->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <th>:</th>
                                            <td>
                                                {{ $oilAndGasPumpSell->description }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Sell</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($oilAndGasPumpSell->oagpSellItems as $oagpSellItemIndex => $oagpSellItem)
                                            <tr>
                                                <td>{{ $oagpSellItemIndex + 1 }}</td>
                                                <td>
                                                    {{ $oagpSellItem->product->name }}
                                                    @if ($oagpSellItem->product->type == "Oil")
                                                        {{ $setting["oagpSetting"]["oil_unit"] }}
                                                    @endif
                                                    @if ($oagpSellItem->product->type == "Gas")
                                                        {{ $setting["oagpSetting"]["gas_unit"] }}
                                                    @endif
                                                </td>

                                                <td>{{ $oagpSellItem->quantity }}</td>
                                                <td>
                                                    {{ $oagpSellItem->price }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th>Total price</th>
                                            <td>
                                                {{ $oilAndGasPumpSell->totalPrice() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th>Total payable</th>
                                            <td>
                                                {{ $oilAndGasPumpSell->totalPayableAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th>Paid amount</th>
                                            <td>
                                                {{ $oilAndGasPumpSell->totalPaidAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th>Due amount</th>
                                            <td>
                                                {{ $oilAndGasPumpSell->totalDueAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th colspan="2"></th>
                                            <th>Income</th>
                                            <td>
                                                {{ $oilAndGasPumpSell->totalIncome() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <div class="card border-secondary">
                        <div class="d-flex justify-content-center mt-2">
                            <h5>Payments</h5>
                        </div>

                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Amount</th>
                                            <th>Notes</th>
                                            <th>Pay at</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($oilAndGasPumpSell->oagpSellPayments as $oagpSellPaymentIndex => $oagpSellPayment)
                                            <tr>
                                                <td>{{ $oagpSellPaymentIndex + 1 }}</td>
                                                <td>
                                                    {{ $oagpSellPayment->amount }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                                </td>
                                                <td>
                                                    <ul>
                                                        @foreach ($oagpSellPayment->note as $perNote)
                                                            <li> {{ $perNote }}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td>
                                                    {{ ($oagpSellPayment->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($oagpSellPayment->created_at))." at ".date('h:i:s a',strtotime($oagpSellPayment->created_at)) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 25%;">Note</th>
                                                    <th style="width: 1%;">:</th>
                                                    <td>
                                                        <ul>
                                                            @foreach ($oilAndGasPumpSell->note as $perNote)
                                                                <li>{{ $perNote }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 25%;">Status</th>
                                                    <th style="width: 1%;">:</th>
                                                    <td>{{ $oilAndGasPumpSell->status }}</td>
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
                                            <td>{{ ($oilAndGasPumpSell->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($oilAndGasPumpSell->created_at))." at ".date('h:i:s a',strtotime($oilAndGasPumpSell->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($oilAndGasPumpSell->created_by_id == null) ? "Not added yet." : $oilAndGasPumpSell->createdBy->name }}</td>
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
                                            <td>{{ ($oilAndGasPumpSell->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($oilAndGasPumpSell->updated_at))." at ".date('h:i:s a',strtotime($oilAndGasPumpSell->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($oilAndGasPumpSell->updated_at == null) ? "Not updated yet." : (($oilAndGasPumpSell->updatedBy() == null) ? "Unknown" : $oilAndGasPumpSell->updatedBy()->name) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $oilAndGasPumpSell->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $oilAndGasPumpSell->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($oilAndGasPumpSell->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
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
                    @if ((Auth::user()->hasUserPermission(["OAGPSEMP04"]) == true) && ($oilAndGasPumpSell->status == "Due"))
                        <a href="{{ route("oil.and.gas.pump.sell.add.payment",["oagpSlug" => $oilAndGasPumpSell->oilAndGasPump->slug, "seSlug" => $oilAndGasPumpSell->slug]) }}" class="btn btn-secondary">Add payment</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("oil.and.gas.pump.sell.index",["oagpSlug" => $oilAndGasPumpSell->oilAndGasPump->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to sell
                </a>
            </div>
        </div>
    </div>
@endsection
