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

            <div class="row">
                <div class="card-body">
                    <input type="text" class="form-control mb-2" name="selected_nav" id="selectedNavInput" value="OilAndGasPump" readonly>
                    <ul class="nav nav-tabs" id="incomeNavTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="oagpIncomeNavTab" data-bs-toggle="tab" data-bs-target="#oagpIncomeNavTabPanel" type="button" role="tab" aria-controls="oagpIncomeNavTabPanel" aria-selected="true">Oil and gas pump</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="projectContractNavTab" data-bs-toggle="tab" data-bs-target="#projectContractNavTabPanel" type="button" role="tab" aria-controls="projectContractNavTabPanel" aria-selected="false">Project contract</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="incomeNavTabsContent">
                        <div class="tab-pane fade show active" id="oagpIncomeNavTabPanel" role="tabpanel" aria-labelledby="oagpIncomeNavTab" tabindex="0">
                            <div class="card-body mb-2">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="row">
                                            <label class="col-md-4 col-form-label col-form-label-sm">Pagination</label>
                                            <div class="col-md-8">
                                                <select class="form-control form-control-sm" id="paginationInput" name="pagination">
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
                                            <label class="col-md-4 col-form-label col-form-label-sm">Oil and gas pump</label>
                                            <div class="col-md-8">
                                                <select class="form-control form-control-sm" id="oilAndGasPumpInput" name="oil_and_gas_pump">
                                                    <option value="">Select</option>
                                                    <option value="All">All</option>
                                                    @foreach ( $oilAndGasPumps as $perOilAndGasPump)
                                                        <option value="{{ $perOilAndGasPump->slug }}">{{ $perOilAndGasPump->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="row">
                                            <label class="col-md-4 col-form-label col-form-label-sm">Start date</label>
                                            <div class="col-md-8">
                                                <input type="date" class="form-control form-control-sm" id="oagpStartDateInput" name="start_date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="row">
                                            <label class="col-md-4 col-form-label col-form-label-sm">End date</label>
                                            <div class="col-md-8">
                                                <input type="date" class="form-control form-control-sm" id="oagpEndDateInput" name="end_date">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card-body">
                                @php
                                    $oagpGridTotalIncome = 0;
                                @endphp
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Oil and gas pump</th>
                                                <th>Product</th>
                                                <th>Date</th>
                                                <th>Income</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($oilAndGasPumpIncomes as $perOilAndGasPumpIncomeIndex => $perOilAndGasPumpIncome)
                                                @php
                                                    $oagpGridTotalIncome +=  $perOilAndGasPumpIncome->totalSellIncome();
                                                @endphp
                                                <tr>
                                                    <td>{{ $perOilAndGasPumpIncomeIndex + 1 }}</td>
                                                    <td>{{ $perOilAndGasPumpIncome->oilAndGasPump->name}}</td>
                                                    <td>{{ $perOilAndGasPumpIncome->oilAndGasPump->name}}</td>
                                                    <td>{{ date('d-M-Y',$perOilAndGasPumpIncome->oilAndGasPump->date) }}</td>
                                                    <td>{{ $perOilAndGasPumpIncome->totalSellIncome() }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">
                                                        <b class="d-flex justify-content-center text-warning">No income foound.</b>
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <span class="d-flex justify-content-end">
                                                        <b>Grid total</b>
                                                    </span>
                                                </td>
                                                <td>{{ $oagpGridTotalIncome }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <span class="d-flex justify-content-end">
                                                        <b>Total</b>
                                                    </span>
                                                </td>
                                                <td>{{ $oagpTotalIncome }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- @if ($oilAndGasPumpIncomes->count() > 0)
                                    <div id="oagpIncomeNavPaginationDiv" class="mb-1">
                                        {{ $oilAndGasPumpIncomes->links() }}
                                    </div>
                                @endif --}}
                            </div>
                        </div>
                        <div class="tab-pane fade" id="projectContractNavTabPanel" role="tabpanel" aria-labelledby="projectContractNavTab" tabindex="0">...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("onPageExtraScript")
    <script>
        $(document).ready(function(){
            $(document).on('click', "#oagpIncomeNavTab", function () {
                $("#selectedNavInput").val(null);
                $("#selectedNavInput").val("OilAndGasPump");
            });

            $(document).on('click', "#projectContractNavTab", function () {
                $("#selectedNavInput").val(null);
                $("#selectedNavInput").val("ProjectContract");
            });

            $(document).on('click', "#generateReportDataTableGridViewButton", function () {
                if(passInputFieldValidation() == true){
                    dataTableLoad(parameterGenerate());
                }
            });
        });
        function parameterGenerate(){
            var parameterString = null;
            $.each( [
                    "incomeReportIndexInput",
                    "oilAndGasPumpInput",
                    "projectContractInput",
                    "startDateInput",
                    "endDateInput",
                    "startDateInput",
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
                url: "{{ route('report.income.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");

                    $("#dataTableGridViewDiv").html($(result).find("#dataTableGridViewDiv").html());
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
                    "incomeReportIndexInput",
                    "oilAndGasPumpInput",
                    "projectContractInput",
                    "startDateInput",
                    "endDateInput",
                    "startDateInput",
                ], function( key, perInput ) {
                if(($("#" + perInput).val().length == 0) && !($("#" + perInput).prop('disabled')) ){
                    passValidation = false;
                    var inputName = $("#" + perInput).attr("name");
                    var inputNameFotmat = inputName.replaceAll("_", " ");
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
