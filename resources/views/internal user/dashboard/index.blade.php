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
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <h5 class="card-title">OAGP Sell quick view - Current month</h5>
                            </div>
                            <div class="d-flex justify-content-center">
                                <h6 class="card-title">{{ date('d-M-Y',strtotime(now()->startOfMonth())) }} to {{ date('d-M-Y',strtotime(now()->endOfMonth())) }}</h6>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-primary">Total price : {{ $oagpSellCMQuickInfo['total_price'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-primary">Total payable : {{ $oagpSellCMQuickInfo['total_payable_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-warning">Total due : {{ $oagpSellCMQuickInfo['total_due_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total paid : {{ $oagpSellCMQuickInfo['total_paid_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total income : {{ $oagpSellCMQuickInfo['total_income'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2"></div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-warning">Total due count : {{ $oagpSellCMQuickInfo['total_due_payment_count'] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total complete count : {{ $oagpSellCMQuickInfo['total_complete_payment_count'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <h5 class="card-title">OAGP Sell quick view - Current week</h5>
                            </div>

                            <div class="d-flex justify-content-center">
                                <h6 class="card-title">{{ date('d-M-Y',strtotime(now()->startOfWeek())) }} to {{ date('d-M-Y',strtotime(now()->endOfWeek())) }}</h6>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-primary">Total price : {{ $oagpSellCWQuickInfo['total_price'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-primary">Total payable : {{ $oagpSellCWQuickInfo['total_payable_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-warning">Total due : {{ $oagpSellCWQuickInfo['total_due_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total paid : {{ $oagpSellCWQuickInfo['total_paid_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total income : {{ $oagpSellCWQuickInfo['total_income'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2"></div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-warning">Total due count : {{ $oagpSellCWQuickInfo['total_due_payment_count'] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total complete count : {{ $oagpSellCWQuickInfo['total_complete_payment_count'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <h5 class="card-title">OAGP Purchase quick view - Current month</h5>
                            </div>
                            <div class="d-flex justify-content-center">
                                <h6 class="card-title">{{ date('d-M-Y',strtotime(now()->startOfMonth())) }} to {{ date('d-M-Y',strtotime(now()->endOfMonth())) }}</h6>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-primary">Total price : {{ $oagpPurchaseCMQuickInfo['total_price'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-primary">Total payable : {{ $oagpPurchaseCMQuickInfo['total_payable_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-warning">Total due : {{ $oagpPurchaseCMQuickInfo['total_due_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total paid : {{ $oagpPurchaseCMQuickInfo['total_paid_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-warning">Total due count : {{ $oagpPurchaseCMQuickInfo['total_due_payment_count'] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total complete count : {{ $oagpPurchaseCMQuickInfo['total_complete_payment_count'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <h5 class="card-title">OAGP Purchase quick view - Current week</h5>
                            </div>

                            <div class="d-flex justify-content-center">
                                <h6 class="card-title">{{ date('d-M-Y',strtotime(now()->startOfWeek())) }} to {{ date('d-M-Y',strtotime(now()->endOfWeek())) }}</h6>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-primary">Total price : {{ $oagpPurchaseCWQuickInfo['total_price'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-primary">Total payable : {{ $oagpPurchaseCWQuickInfo['total_payable_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-warning">Total due : {{ $oagpPurchaseCWQuickInfo['total_due_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total paid : {{ $oagpPurchaseCWQuickInfo['total_paid_amount'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-warning">Total due count : {{ $oagpPurchaseCWQuickInfo['total_due_payment_count'] }}</button>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <button class="btn btn-sm btn-success">Total complete count : {{ $oagpPurchaseCWQuickInfo['total_complete_payment_count'] }} {{ $setting["businessSetting"]["currency_symbol"] }}</button>
                                </div>
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
