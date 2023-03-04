@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump sell
@endsection

@section('mainCardTitle')
    Add payment
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $oilAndGasPumpSell->oilAndGasPump->slug]) }}">{{ $oilAndGasPumpSell->oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.sell.index",["oagpSlug" => $oilAndGasPumpSell->oilAndGasPump->slug]) }}">Sell</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add payment</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-2">
        <h5 class="card-header">General information</h5>
        <div class="card-body text-dark mb-2">
            <form action="{{ route("oil.and.gas.pump.sell.save.payment",["oagpSlug" => $oilAndGasPump->slug,"seSlug"=>$oilAndGasPumpSell->slug]) }}" method="POST" id="addPaymentForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Amount <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                            <div class="col-lg-8">
                                <input id="amountInput" name="amount" type="number" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ (old('amount') == null) ? 0 : old('amount') }}" min="0" max="{{ $oilAndGasPumpSell->totalDue() }}" step="00.01" required>

                                @error('amount')
                                    <span class="invalid-feedback" role="alert" style="display: block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-4 col-form-label col-form-label-sm text-bold">Payable amount ({{ $setting["businessSetting"]["currency_symbol"] }}) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                            <div class="col-md-8">
                                <input id="payableAmountInput" name="payable_amount" type="number" class="form-control form-control-sm @error('payable_amount') is-invalid @enderror" value="{{ (old('payable_amount') == null) ? $oilAndGasPumpSell->totalPayable() : old('payable_amount') }}" min="0" step="00.01" required readonly>
                                @error('payable_amount')
                                    <span class="invalid-feedback" role="alert" style="display: block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-4 col-form-label col-form-label-sm text-bold">Paid amount ({{ $setting["businessSetting"]["currency_symbol"] }}) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                            <div class="col-md-8">
                                <input id="paidAmountInput" name="paid_amount" type="number" class="form-control form-control-sm @error('paid_amount') is-invalid @enderror" value="{{ (old('paid_amount') == null) ? $oilAndGasPumpSell->totalPaid() : old('paid_amount') }}" min="0" step="00.01" required readonly>
                                @error('paid_amount')
                                    <span class="invalid-feedback" role="alert" style="display: block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-4 col-form-label col-form-label-sm text-bold">Due amount ({{ $setting["businessSetting"]["currency_symbol"] }}) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                            <div class="col-md-8">
                                <input id="dueAmountInput" name="due_amount" type="number" class="form-control form-control-sm @error('due_amount') is-invalid @enderror" value="{{ (old('due_amount') == null) ? $oilAndGasPumpSell->totalDue() : old('due_amount') }}" min="0" step="00.01" required readonly>
                                @error('due_amount')
                                    <span class="invalid-feedback" role="alert" style="display: block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Note <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                            <div class="col-lg-8">
                                <textarea id="noteInput" name="note" class="form-control form-control-sm @error('note') is-invalid @enderror" placeholder="Ex: Hello" required>{{ old('note') }}</textarea>

                                @error('note')
                                    <span class="invalid-feedback" role="alert" style="display: block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4 mb-3">
                        <button type="submit" class="btn btn-outline-success">
                            Save
                        </button>
                    </div>
                </div>
            </form>
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

@push('onPageExtraCss')
    <script>
        $(document).ready(function(){
            $(document).on('change', '#amountInput', function () {
                $(this).val(parseFloat($(this).val()).toFixed(2));

                var amountInput = parseFloat($(this).val());

                var paidInput = parseFloat($("#paidAmountInput").val());
                var dueAmountInput = parseFloat($("#dueAmountInput").val());

                if((dueAmountInput == amountInput) || ( amountInput < dueAmountInput)){
                    $("#paidAmountInput").val((paidInput + amountInput).toFixed(2));
                    $("#dueAmountInput").val((dueAmountInput - amountInput).toFixed(2));
                }
            });
        });
    </script>
@endpush
