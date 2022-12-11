@extends('layouts.app')

@section('mainPageName')
    Dashboard
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <div class="d-flex justify-content-center">
                <h5 class="card-title">Project contract quick view</h5>
            </div>

            <div class="row">
                <div class="col-md-6 mb-2">
                    <p>
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#ongoingProjectContractCollapse" aria-expanded="false" aria-controls="ongoingProjectContractCollapse">
                            Ongoing project contract : {{ $ongoingProjectContractQuickView["projectContract"] }}
                        </button>
                    </p>
                    <div class="collapse" id="ongoingProjectContractCollapse">
                        <div class="card card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered ">
                                    <tbody>
                                        <tr>
                                            <th>Project contract</th>
                                            <td>:</td>
                                            <td>{{ $ongoingProjectContractQuickView["projectContract"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Journal entry</th>
                                            <td>:</td>
                                            <td>{{ $ongoingProjectContractQuickView["journalEntry"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Journal entry (Revenue)</th>
                                            <td>:</td>
                                            <td>{{ $ongoingProjectContractQuickView["journalRevenueEntry"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Journal entry (Loss)</th>
                                            <td>:</td>
                                            <td>{{ $ongoingProjectContractQuickView["journalLossEntry"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Invested amount</th>
                                            <td>:</td>
                                            <td>{{ $ongoingProjectContractQuickView["investedAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Revenue amount</th>
                                            <td>:</td>
                                            <td>{{ $ongoingProjectContractQuickView["revenueAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Loss amount</th>
                                            <td>:</td>
                                            <td>{{ $ongoingProjectContractQuickView["lossAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Receivable amount</th>
                                            <td>:</td>
                                            <td>{{ $ongoingProjectContractQuickView["receivableAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <p>
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#completeProjectContractCollapse" aria-expanded="false" aria-controls="completeProjectContractCollapse">
                            Complete project contract : {{ $completeProjectContractQuickView["projectContract"] }}
                        </button>
                    </p>
                    <div class="collapse" id="completeProjectContractCollapse">
                        <div class="card card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered ">
                                    <tbody>
                                        <tr>
                                            <th>Project contract</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["projectContract"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Journal entry</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["journalEntry"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Journal entry (Revenue)</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["journalRevenueEntry"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Journal entry (Loss)</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["journalLossEntry"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payment</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["payment"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Invested amount</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["investedAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Revenue amount</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["revenueAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Loss amount</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["lossAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Receivable amount</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["receivableAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Receive amount</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["receiveAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Due amount</th>
                                            <td>:</td>
                                            <td>{{ $completeProjectContractQuickView["dueAmount"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
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
        <div class="card-body text-dark">
            <h5 class="card-title">Dark card title 2</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
        </div>
    </div>
@endsection
