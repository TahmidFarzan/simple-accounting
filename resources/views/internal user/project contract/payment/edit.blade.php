@extends('layouts.app')

@section('mainPageName')
    Project contract
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("project.contract.index") }}">Project contract</a></li>
            <li class="breadcrumb-item"><a href="{{ route("project.contract.details",["slug" => $projectContract->slug]) }}">{{ $projectContract->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("project.contract.payment.index",["pcSlug" => $projectContract->slug]) }}">Payment</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')

    @php
        $currentDueAmount = (old("due") == null) ? $projectContract->totalDueAmount() : old("due");
        $currentPaymentMethodOption = (old("payment_method") == null) ? $projectContractPayment->paymentMethod->slug : old("payment_method");
    @endphp

    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("project.contract.payment.update",["pcSlug" => $projectContract->slug,"slug" => $projectContractPayment->slug]) }}" method="POST" id="editForm">
                @csrf
                @method("PATCH")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="nameInput" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ (old('name') == null) ? $projectContractPayment->name :  old('name')}}" placeholder="Ex: Hello" maxlength="200" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Payment date <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="paymentDateInput" name="payment_date" type="date" class="form-control form-control-sm @error('payment_date') is-invalid @enderror" value="{{ (old('payment_date') == null) ? date('Y-m-d',strtotime($projectContractPayment->payment_date)) : date('Y-m-d',strtotime(old('payment_date'))) }}" required>
                                    @error('payment_date')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Payment method <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <select name="payment_method" id="paymentMethodInput" class="form-control form-control-sm @error('payment_method') is-invalid @enderror" required>
                                        <option value="">Select</option>
                                        @foreach ($projectContractPaymentMethods as $perPCPaymentMethod)
                                            <option value="{{ $perPCPaymentMethod->slug }}" @if($perPCPaymentMethod->slug == $currentPaymentMethodOption) selected @endif>{{ $perPCPaymentMethod->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Current amount ({{ $setting["businessSetting"]["currency_symbol"] }})  <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="currentAmountInput" name="current_amount" type="number" class="form-control form-control-sm @error('current_amount') is-invalid @enderror" value="{{ (old('current_amount') == null) ? $projectContractPayment->amount :  old('current_amount')}}" min="0" placeholder="Ex: 0.0" step="0.01" required readonly>
                                    @error('current_amount')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Add more amount ({{ $setting["businessSetting"]["currency_symbol"] }})  <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="amountInput" name="amount" type="number" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ (old('amount') == null) ? null :  old('amount')}}" min="0" placeholder="Ex: 0.0" step="0.01" required>
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
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Due amount ({{ $setting["businessSetting"]["currency_symbol"] }})  <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="dueAmountInput" name="due" type="number" class="form-control form-control-sm @error('due') is-invalid @enderror" value="{{ $currentDueAmount }}" min="0" placeholder="Ex: 0.0" step="0.01" required readonly>
                                    @error('due')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Description</label>
                                <div class="col-lg-8">
                                    <textarea id="descriptionInput" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Ex: Hello">{{ (old('description') == null) ? $projectContractPayment->description : old('description')}}</textarea>

                                    @error('description')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
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
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4 mb-3">
                        <button type="submit" class="btn btn-outline-success">
                            Update
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
                <a role="button" href="{{ route("project.contract.payment.index",["pcSlug" => $projectContract->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to payment
                </a>
            </div>
        </div>
    </div>
@endsection

@push("onPageExtraScript")
    <script>
        $(document).ready(function(){
            $(document).on('change', "#amountInput", function () {
                var dueAmount = '{{ $projectContract->totalDueAmount() }}';
                if($(this).val().length > 0){
                    dueAmount = parseFloat(dueAmount) - parseFloat($(this).val());
                }

                $("#dueAmountInput").val(dueAmount);
            });
        });
    </script>
@endpush
