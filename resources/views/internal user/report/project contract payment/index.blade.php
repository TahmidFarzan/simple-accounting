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
            <li class="breadcrumb-item">Report</li>
            <li class="breadcrumb-item active" aria-current="page">Project contract payment</li>
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
                        <label class="col-md-4 col-form-label col-form-label-sm">Payment date</label>
                        <div class="col-md-8">
                            <div class="input-group mb-3">
                                <input type="date" class="form-control form-control-sm" id="paymentDateInputForGenerateReport" name="payment_date">
                                <select id="paymentDateConditionInputForGenerateReport" name="payment_date_condition" class="form-control form-control-sm">
                                    @foreach (array(""=>"Select","="=>"Equal","<"=>"Less than",">"=>"Greater than","<="=>"Equal or less than",">="=>"Equal or greater than") as $perConditionIndex => $perCondition)
                                        <option value="{{ $perConditionIndex }}">{{ $perCondition }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="paymentDateInputForGenerateReportErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                            <div id="paymentDateConditionInputForGenerateReportErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Payment method</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="paymentMethodInputForGenerateReport" name="payment_method">
                                <option value="">Select</option>
                                <option value="All">All</option>
                                @foreach ( $projectContractPaymentMethods as $perProjectContractPaymentMethod)
                                    <option value="{{ $perProjectContractPaymentMethod->slug }}">{{ $perProjectContractPaymentMethod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Search</label>
                        <div class="col-md-8">
                            <input type="search" class="form-control form-control-sm" placeholder="Search value." name="search" id="searchInputForGenerateReport">
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
            @if ($projectContractPayments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Payment date</th>
                                <th>Payment method</th>
                                <th>Description</th>
                                <th>Note</th>
                                <th>Amount ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                <th>Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projectContractPayments as $perProjectContractPaymentIndex => $perProjectContractPayment)
                                <tr>
                                    <td>{{ $perProjectContractPaymentIndex +1 }}</td>
                                    <td>{{ $perProjectContractPayment->name }}</td>
                                    <td>{{date('d-M-Y',strtotime($perProjectContractPayment->payment_date)) }} </td>
                                    <td>{{ $perProjectContractPayment->paymentMethod->name }}</td>
                                    <td>{{($perProjectContractPayment->description == null) ? "Not added yet." : $perProjectContractPayment->description }}</td>
                                    <td>
                                        <ul>
                                            @forelse ($perProjectContractPayment->note as $perNote)
                                                <li>{{ $perNote }}</li>
                                            @empty
                                                <li><b class="d-flex justify-content-center text-warning">Not added.</b></li>
                                            @endforelse
                                        </ul>
                                    </td>
                                    <td>{{ $perProjectContractPayment->amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                    <td><a href="{{ route("report.project.contract.payment.details",["slug" => $perProjectContractPayment->slug]) }}" class="btn btn-sm btn-info m-1">Details</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <b class="d-flex justify-content-center text-warning">No project contract method found.</b>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="paginationDiv" class="mb-1">
                    {{ $projectContractPayments->links() }}
                </div>
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
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#generateReportDataTableGridViewButton", function () {
                dataTableLoad(parameterGenerate());
            });
        });

        function parameterGenerate(){
            var parameterString = null;
            $.each( [
                    "paginationInputForGenerateReport",
                    "paymentDateInputForGenerateReport",
                    "paymentDateConditionInputForGenerateReport",
                    "searchInputForGenerateReport",
                    "paymentMethodInputForGenerateReport",
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
                url: "{{ route('report.project.contract.payment.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");;

                    $("#generateReportDataTableGridViewDiv").html($(result).find("#generateReportDataTableGridViewDiv").html());
                },
                error: function(errorResponse) {
                    showExtraErrorMessages(["Error " + errorResponse.status,errorResponse.statusText]);
                }
            });
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
