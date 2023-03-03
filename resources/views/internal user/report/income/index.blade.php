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
                    <input type="text" class="form-control mb-2" name="selected_nav_tab" id="selectedNavInput" value="OilAndGasPump" readonly hidden>
                    <ul class="nav nav-tabs" id="incomeNavTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="oagpIncomeNavTab" data-bs-toggle="tab" data-bs-target="#oagpIncomeNavTabPanel" type="button" role="tab" aria-controls="oagpIncomeNavTabPanel" aria-selected="true">Oil and gas pump</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="projectContractNavTab" data-bs-toggle="tab" data-bs-target="#pcIncomeNavTabPanel" type="button" role="tab" aria-controls="pcIncomeNavTabPanel" aria-selected="false">Project contract</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="incomeNavTabsContent">
                        <div class="tab-pane fade show active" id="oagpIncomeNavTabPanel" role="tabpanel" aria-labelledby="oagpIncomeNavTab" tabindex="0">
                            <div class="card-body mb-2">
                                <div class="row">
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

                                    <div class="col-md-6 mb-2"></div>

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

                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-primary" id="oagpIncomeGenerateButton">Generate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" id="oagpIncomeDataTableDiv">
                                <div class="row">
                                    @php $oagpIncomeSellIndexCount = 0; @endphp
                                    @foreach ($oilAndGasPumpSellIncomes as $oagpSellIndex  => $oagpSellIncomes)
                                        @php $oagpIncomeSellIndexCount ++; @endphp
                                        <div class="col-md-6 {{ ($oilAndGasPumpSellIncomes->count() < $oagpIncomeSellIndexCount ) }}">
                                            <div class="card border-dark">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-center mb-2">
                                                        <b>{{ $oagpSellIndex }}</b>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sl</th>
                                                                    <th>Date</th>
                                                                    <th>Income</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $oagpTotalIncome = 0;
                                                                    $oagpSellIncomeIndexCount = 0;
                                                                @endphp
                                                                @forelse ($oagpSellIncomes as $oagpSellIncomeIndex => $oagpSellIncome )
                                                                    @php
                                                                        $oagpSellIncomeIndexCount ++;
                                                                        $oagpTotalIncome += $oagpSellIncome;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $oagpSellIncomeIndexCount }}</td>
                                                                        <td>{{ date("d-M-Y",strtotime($oagpSellIncomeIndex))  }}</td>
                                                                        <td>{{  $oagpSellIncome }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="3">
                                                                            <div class="d-flex justify-content-center me-2 text-warning">
                                                                                <b>No income found</b>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="2">
                                                                        <div class="d-flex justify-content-end me-2">
                                                                            <b>Total</b>
                                                                        </div>
                                                                    </th>
                                                                    <th>{{ $oagpTotalIncome }}</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pcIncomeNavTabPanel" role="tabpanel" aria-labelledby="projectContractNavTab" tabindex="0">
                            <div class="card-body mb-2">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="row">
                                            <label class="col-md-4 col-form-label col-form-label-sm">Start date</label>
                                            <div class="col-md-8">
                                                <input type="date" class="form-control form-control-sm" id="pcStartDateInput" name="start_date" value="{{ date('Y-m-d',strToTime(now())) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="row">
                                            <label class="col-md-4 col-form-label col-form-label-sm">End date</label>
                                            <div class="col-md-8">
                                                <input type="date" class="form-control form-control-sm" id="pcEndDateInput" name="end_date" value="{{ date('Y-m-d',strToTime(now())) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-primary" id="pcIncomeGenerateButton">Generate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" id="pcIncomeDataTableDiv">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Start date</th>
                                                <th>End date</th>
                                                <th>Project</th>
                                                <th>Income</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $projectContractTotalIncome = 0;
                                            @endphp
                                            @forelse ($projectContractIncomes as $startDate => $projectContractIncomesData)
                                                @forelse ($projectContractIncomesData as $endDate => $projectContractIncomesRow)
                                                    @forelse ($projectContractIncomesRow as $projectContractIncomeIndex => $projectContractIncome)
                                                        @php
                                                            $projectContractTotalIncome += $projectContractIncome->totalIncome();
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $projectContractIncomeIndex + 1 }}</td>
                                                            <td>{{ date('d-M-Y',strToTime($startDate)) }}</td>
                                                            <td>{{ date('d-M-Y',strToTime($endDate)) }}</td>
                                                            <td>{{ $projectContractIncome->name }}</td>
                                                            <td>{{ $projectContractIncome->totalIncome() }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5"><b class="d-flex justify-content-center text-warning">No income found.</b></td>
                                                        </tr>
                                                    @endforelse
                                                @empty
                                                    <tr>
                                                        <td colspan="5"><b class="d-flex justify-content-center text-warning">No income found.</b></td>
                                                    </tr>
                                                @endforelse
                                            @empty
                                                <tr>
                                                    <td colspan="5"><b class="d-flex justify-content-center text-warning">No income found.</b></td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <th colspan="4">
                                                <div class="d-flex justify-content-end me-2">
                                                    <b>Total income</b>
                                                </div>
                                            </th>
                                            <th> {{ $projectContractTotalIncome }} {{ $setting["businessSetting"]["currency_symbol"] }}</th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
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

            $(document).on('click', "#oagpIncomeGenerateButton", function () {
                if(oagpIncomePassInputFieldValidation() == true){
                    oagpIncomeDataTableLoad(oagpIncomeParameterGenerate());
                }
            });

            $(document).on('click', "#pcIncomeGenerateButton", function () {
                if(pcIncomePassInputFieldValidation() == true){
                    pcIncomeDataTableLoad(pcIncomeParameterGenerate());
                }
            });
        });

        function oagpIncomeParameterGenerate(){
            var parameterString = null;
            $.each( [
                    "selectedNavInput",
                    "oilAndGasPumpInput",
                    "oagpStartDateInput",
                    "oagpEndDateInput",
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

        function pcIncomeParameterGenerate(){
            var parameterString = null;
            $.each( [
                    "selectedNavInput",
                    "pcStartDateInput",
                    "pcEndDateInput",
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

        function oagpIncomeDataTableLoad(parameterString){
            $.ajax({
                type: "get",
                url: "{{ route('report.income.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");
                    var selectedNavInput = $("#selectedNavInput").val();

                    if(selectedNavInput == "OilAndGasPump"){
                        $("#oagpIncomeNavTabPanel #oagpIncomeDataTableDiv").html($(result).find("#oagpIncomeNavTabPanel #oagpIncomeDataTableDiv").html());
                    }

                },
                error: function(errorResponse) {
                    showExtraErrorMessages(["Error " + errorResponse.status,errorResponse.statusText]);
                }
            });
        }

        function pcIncomeDataTableLoad(parameterString){
            $.ajax({
                type: "get",
                url: "{{ route('report.income.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");
                    $("#pcIncomeNavTabPanel #pcIncomeDataTableDiv").html($(result).find("#pcIncomeNavTabPanel #pcIncomeDataTableDiv").html());
                },
                error: function(errorResponse) {
                    showExtraErrorMessages(["Error " + errorResponse.status,errorResponse.statusText]);
                }
            });
        }

        function oagpIncomePassInputFieldValidation(){
            var passValidation = true;
            var errors = [];
            $.each( [
                    "selectedNavInput",
                    "oilAndGasPumpInput",
                    "oagpStartDateInput",
                    "oagpEndDateInput",
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

        function pcIncomePassInputFieldValidation(){
            var passValidation = true;
            var errors = [];
            $.each( [
                    "selectedNavInput",
                    "pcStartDateInput",
                    "pcEndDateInput",
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
