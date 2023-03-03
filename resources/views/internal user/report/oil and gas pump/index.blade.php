@extends('layouts.app')

@section('mainPageName')
    Report
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item">Report</li>
            <li class="breadcrumb-item active" aria-current="page">Oil and gas pumb</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">
        <div class="card-body text-dark mb-2">
            <div class="row mb-2">
                <div class="row mb-2" id="extraErrorMessageDiv" style="display: none;"></div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Pagination</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="paginationInputForGenerateReport" name="pagination">
                                <option value="">Select</option>
                                @foreach ( $paginations as $perPagination)
                                    <option value="{{ $perPagination }}">{{ $perPagination }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Model</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="modelInputForGenerateReport" name="model">
                                <option value="">Select</option>
                                @foreach ( $models as $perModelIndex => $perModel)
                                    <option value="{{ $perModelIndex }}">{{ $perModel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Oil and gas pump</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="oilAndGasPumpInputForGenerateReport" name="oil_and_gas_pump">
                                <option value="">Select</option>
                                <option value="All">All</option>
                                @foreach ( $oilAndGasPumps as $perOilAndGasPump)
                                    <option value="{{ $perOilAndGasPump->slug }}">{{ $perOilAndGasPump->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2" id="supplierInputDivForGenerateReport" style="display: none;">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Supplier</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="supplierInputForGenerateReport" name="supplier" disabled hidden>
                                <option value="">Select</option>
                                <option value="All">All</option>
                                @foreach ( $suppliers as $perSupplier)
                                    <option value="{{ $perSupplier->slug }}">{{ $perSupplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Start date</label>
                        <div class="col-md-8">
                            <input type="date" class="form-control form-control-sm" id="startDateInputForGenerateReport" name="start_date">
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">End date</label>
                        <div class="col-md-8">
                            <input type="date" class="form-control form-control-sm" id="endDateInputForGenerateReport" name="end_date">
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="d-flex justify-content-center">
                        <button type="button" id="generateReportDataTableGridViewButton" class="btn btn-success"><i class="fa-solid fa-list"></i> Generate report</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body text-dark" id="generateReportDataTableGridViewDiv">
            @if (($modelRecords->count() > 0) && ($generatedReport ==true))
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>OAGP</th>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created by</th>
                                <th>Discount ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                <th>Total ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                <th>Payable Total ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                <th>Paid ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allRowTotalPrice = 0;
                                $allRowTotalPaidAmount = 0;
                                $allRowTotalPayableAmount = 0;
                            @endphp

                            @forelse ($modelRecords as $perModelRecordIndex => $perModelRecord)
                                <tr>
                                    <td>{{ $perModelRecordIndex + 1 }}</td>
                                    <td>{{ $perModelRecord->oilAndGasPump->name }}</td>
                                    <td>{{ $perModelRecord->invoice }}</td>
                                    <td>{{ ( ($perModelRecord->date == null) ? "Not added." : date('d-M-Y',strtotime($perModelRecord->date)) ) }}</td>
                                    <td>{{ $perModelRecord->name }}</td>
                                    <td>{{ $perModelRecord->status }}</td>
                                    <td>{{ $perModelRecord->createdBy->name }}</td>
                                    <td>{{ $perModelRecord->discount }}</td>
                                    <td>

                                        @php
                                            if (str_contains(Str::studly($selectedModel ), "Purchase")) {
                                                $allRowTotalPrice = $allRowTotalPrice + $perModelRecord->totalPrice();
                                            }

                                            if (str_contains(Str::studly($selectedModel ), "Sell")) {
                                                $allRowTotalPrice = $allRowTotalPrice + $perModelRecord->totalPrice();
                                            }
                                        @endphp

                                        @if (str_contains(Str::studly($selectedModel ), "Purchase"))
                                            {{ $perModelRecord->totalPrice() }}
                                        @endif

                                        @if (str_contains(Str::studly($selectedModel ), "Sell"))
                                            {{ $perModelRecord->totalPrice() }}
                                        @endif

                                        {{ $setting["businessSetting"]["currency_symbol"] }}
                                    </td>
                                    <td>
                                        @php
                                            if (str_contains(Str::studly($selectedModel ), "Purchase")) {
                                                $allRowTotalPayableAmount = $allRowTotalPayableAmount + $perModelRecord->totalPayableAmount();
                                            }

                                            if (str_contains(Str::studly($selectedModel ), "Sell")) {
                                                $allRowTotalPayableAmount = $allRowTotalPayableAmount + $perModelRecord->totalPayableAmount();
                                            }
                                        @endphp

                                        @if (str_contains(Str::studly($selectedModel ), "Purchase"))
                                            {{ $perModelRecord->totalPayableAmount() }}
                                        @endif

                                        @if (str_contains(Str::studly($selectedModel ), "Sell"))
                                            {{ $perModelRecord->totalPayableAmount() }}
                                        @endif

                                        {{ $setting["businessSetting"]["currency_symbol"] }}
                                    </td>
                                    <td>
                                        @php
                                            if (str_contains(Str::studly($selectedModel ), "Purchase")) {
                                                $allRowTotalPaidAmount = $allRowTotalPaidAmount + $perModelRecord->totalPaidAmount();
                                            }

                                            if (str_contains(Str::studly($selectedModel ), "Sell")) {
                                                $allRowTotalPaidAmount = $allRowTotalPaidAmount + $perModelRecord->totalPaidAmount();
                                            }
                                        @endphp

                                        @if (str_contains(Str::studly($selectedModel ), "Purchase"))
                                            {{ $perModelRecord->totalPaidAmount() }}
                                        @endif

                                        @if (str_contains(Str::studly($selectedModel ), "Sell"))
                                            {{ $perModelRecord->totalPaidAmount() }}
                                        @endif

                                        {{ $setting["businessSetting"]["currency_symbol"] }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11">
                                        <b class="d-flex justify-content-center text-warning">No records found.</b>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8">
                                    <b class=" d-flex float-end">Total</b>
                                </th>
                                <th>{{ $allRowTotalPrice }} {{ $setting["businessSetting"]["currency_symbol"] }}</th>
                                <th>{{ $allRowTotalPayableAmount }} {{ $setting["businessSetting"]["currency_symbol"] }}</th>
                                <th>{{ $allRowTotalPaidAmount }} {{ $setting["businessSetting"]["currency_symbol"] }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div id="paginationDiv" class="mb-1">
                    {{ $modelRecords->links() }}
                </div>
            @endif

            @if (($modelRecords->count() == 0) && ($generatedReport == true))
                <b class="d-flex justify-content-center text-warning">No records found.</b>
            @endif
        </div>
    </div>
@endsection

@push("onPageExtraScript")
    <script>
        $(document).ready(function(){

            $(document).on('click', "#paginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                if(passInputFieldValidation() == true){
                    dataTableLoad(paginationParameter);
                }
            });

            $(document).on('click', "#generateReportDataTableGridViewButton", function () {
                if(passInputFieldValidation() == true){
                    dataTableLoad(parameterGenerate());
                }
            });

            $(document).on('change', "#modelInputForGenerateReport", function () {
                var modelValue = $(this).val();

                if((modelValue != '' ) && (modelValue.includes("Purchase") == true)){
                    $("#supplierInputForGenerateReport").prop('disabled',false);
                    $("#supplierInputForGenerateReport").prop('hidden',false);

                    $("#supplierInputDivForGenerateReport").show();
                }
                if((modelValue != '' ) && (modelValue.includes("Purchase") == false)){
                    $("#supplierInputForGenerateReport").prop('disabled',true);
                    $("#supplierInputForGenerateReport").prop('hidden',true);

                    $("#supplierInputDivForGenerateReport").hide();
                }
            });

            $(document).on('change', "#oilAndGasPumpInputForGenerateReport", function () {
                var oagp = $(this).val();

                if((oagp != '') && (oagp != 'All')){
                    $.ajax({
                        type: "get",
                        url: "{{ route('report.oil.and.gas.pump.get.supplier.by.oagp') }}",
                        data:{ "oil_and_gas_pump" : oagp },
                        success: function(successResponce) {
                            $("#supplierInputForGenerateReport").html("");
                            $("#supplierInputForGenerateReport").append("<option>Select</option>");
                            $("#supplierInputForGenerateReport").append('<option value="All">All</option>');

                            $.each(successResponce, function( index, value ) {
                                $("#supplierInputForGenerateReport").append('<option value="'+value["slug"]+'">'+value["name"]+'</option>');
                            });
                        },
                        error: function(errorResponse) {
                            showExtraErrorMessages(["Error " + errorResponse.status,errorResponse.statusText]);
                        }
                    });
                }
            });
        });

        function parameterGenerate(){
            var parameterString = null;
            $.each( [
                    "paginationInputForGenerateReport",
                    "modelInputForGenerateReport",
                    "startDateInputForGenerateReport",
                    "endDateInputForGenerateReport",
                    "supplierInputForGenerateReport",
                    "oilAndGasPumpInputForGenerateReport",
                ], function( key, perInput ) {
                if(($("#" + perInput).val().length > 0)){
                    var inputFieldValue = $("#" + perInput).val();
                    var inputFieldName = $("#" + perInput).attr('name');
                    var curentParameterString = inputFieldName + "=" + inputFieldValue;
                    parameterString = (parameterString==null) ? curentParameterString : parameterString + "&" + curentParameterString;
                }
            });
            return parameterString;
        }

        function dataTableLoad(parameterString){
            $.ajax({
                type: "get",
                url: "{{ route('report.oil.and.gas.pump.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");

                    $("#generateReportDataTableGridViewDiv").html($(result).find("#generateReportDataTableGridViewDiv").html());
                },
                error: function(errorResponse) {
                    showExtraErrorMessages(["Error " + errorResponse.status,errorResponse.statusText]);
                }
            });
        }

        function passInputFieldValidation(){
            var passValidation = true;
            var errors = [];
            $.each( [
                    "paginationInputForGenerateReport",
                    "modelInputForGenerateReport",
                    "startDateInputForGenerateReport",
                    "endDateInputForGenerateReport",
                    "supplierInputForGenerateReport",
                ], function( key, perInput ) {
                if(($("#" + perInput).val().length == 0) && ($("#" + perInput).attr('disabled')!== false)){
                    passValidation = false;
                    var inputName = $("#" + perInput).attr("name");
                    var inputNameFotmat = inputName.replace("_", " ");
                    errors.push("The " + inputNameFotmat + " field is empty.");
                }
            });

            if(errors.length > 0){
                showExtraErrorMessages(errors);
            }

            return passValidation;
        }

        function showExtraErrorMessages(errorMessages){
            if(errorMessages.length > 0){
                $("#extraErrorMessageDiv").show();
                $("#extraErrorMessageDiv").html('<div class="p-3"><div class="alert-messages alert alert-danger" role="alert"><div class="row"><div class="col-11 col-lg-11 col-md-11 col-sm-11" id="apiErrorMessageDiv"></div><div class="p-1 col-1 col-lg-1 col-md-1 col-sm-1"><button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div></div></div>');
                var errorMessageDiv = "#extraErrorMessageDiv .p-3 .alert-danger .row .col-md-11";

                $(errorMessageDiv).html("");
                $(errorMessageDiv).html("<ul></ul>");
                $( errorMessages).each(function( index,perError ) {
                    $(errorMessageDiv + " ul").append( "<li>"+perError+"</li>");
                });
            }
            else{
                $("#extraErrorMessageDiv").hide();
                $("#extraErrorMessageDiv").html("");
            }
        }
    </script>
@endpush
