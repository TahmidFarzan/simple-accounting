@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump purchase
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $oilAndGasPump->slug]) }}">{{ $oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item">Purchase</li>
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
                    @if (Auth::user()->hasUserPermission(["OAGPPUMP02"]) == true)
                        <a href="{{ route("oil.and.gas.pump.purchase.add",["oagpSlug" => $oilAndGasPump->slug]) }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add purchase</a>
                    @endif

                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#sortingCollapseDiv" aria-expanded="false" aria-controls="sortingCollapseDiv">
                        <i class="fa-solid fa-sort"></i> Sorting
                    </button>
                </p>
            </div>
            <div class="row mb-2" id="extraErrorMessageDiv" style="display: none;"></div>

            <div class="row mb-2">
                <div class="collapse mb-2" id="sortingCollapseDiv">
                    <div class="card card-body">
                        <div class="row mb-2">
                            <div class="col-md-12 mb-2" hidden>
                                <input type="text" name="selected_nav_tab" id="selectedNavTabForSorting" class="form-control form-control-sm" readonly required value="Complete" hidden>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-md-4 col-form-label col-form-label-sm">Pagination</label>
                                    <div class="col-md-8">
                                        <select class="form-control form-control-sm" id="paginationInputForSorting" name="pagination">
                                            <option value="">Select</option>
                                            @foreach ( $paginations as $perPagination)
                                                <option value="{{ $perPagination }}">{{ $perPagination }}</option>
                                            @endforeach
                                        </select>
                                        <div id="paginationInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-md-4 col-form-label col-form-label-sm">Search</label>
                                    <div class="col-md-8">
                                        <div class="input-group mb-3">
                                            <input type="search" class="form-control form-control-sm" placeholder="Search value." name="search" id="searchInputForSorting">
                                            <button class="btn btn-sm btn-outline-primary" type="button" id="searchButton">Go</button>
                                        </div>
                                        <div id="searchInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <nav>
                    <div class="nav nav-tabs" id="tabGroup" role="tabList">
                        <button class="nav-link active" id="completeNavTab" data-bs-toggle="tab" data-bs-target="#completeNavTabDiv" type="button" role="tab">Complete</button>
                        <button class="nav-link" id="dueNavTab" data-bs-toggle="tab" data-bs-target="#dueNavTabDiv" type="button" role="tab">Due</button>
                    </div>
                </nav>
                <div class="tab-content" id="tabGroupDivContent">
                    <div class="tab-pane fade show active" id="completeNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice</th>
                                            <th>Supplier</th>
                                            <th>Date</th>
                                            <th>Total payable amount</th>
                                            <th>Paid amount</th>
                                            <th>Due amount</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($completeOAGPPurchases as $perOAGPPurchaseIndex => $perOAGPPurchase)
                                            <tr>
                                                <td>{{ $perOAGPPurchaseIndex + 1 }}</td>
                                                <td>{{ $perOAGPPurchase->invoice }}</td>
                                                <td>{{ $perOAGPPurchase->oagpSupplier->name }}</td>
                                                <td>{{ $perOAGPPurchase->date }}</td>
                                                <td>
                                                    @php
                                                        $totalPayableAmountInformation = "<p>";
                                                        $totalPayableAmountInformation = $totalPayableAmountInformation.'<b>Total price :</b> '.$perOAGPPurchase->oagpTotalPrice()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $totalPayableAmountInformation = $totalPayableAmountInformation.'<b>Discount :</b> '.$perOAGPPurchase->discount.'% <br/>';
                                                        $totalPayableAmountInformation = $totalPayableAmountInformation."</p>";
                                                    @endphp

                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="date-range-popover" data-bs-title="Date range information" data-bs-content="{{ $totalPayableAmountInformation }}">
                                                        {{ $perOAGPPurchase->oagpPayableAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                                    </button>
                                                </td>
                                                <td>{{ $perOAGPPurchase->paid_amount }} {{ $setting["businessSetting"]["currency_symbol"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                <td>{{ $perOAGPPurchase->oagpDueAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                <td>Detail | Edit</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">
                                                    <b class="d-flex justify-content-center text-warning">No purchase found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div id="completeNavTabPaginationDiv" class="mb-1">
                                {{ $completeOAGPPurchases->links() }}
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="dueNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice</th>
                                            <th>Supplier</th>
                                            <th>Date</th>
                                            <th>Total payable amount</th>
                                            <th>Paid amount</th>
                                            <th>Due amount</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dueOAGPPurchases as $perOAGPPurchaseIndex => $perOAGPPurchase)
                                            <tr>
                                                <td>{{ $perOAGPPurchaseIndex + 1 }}</td>
                                                <td>{{ $perOAGPPurchase->invoice }}</td>
                                                <td>{{ $perOAGPPurchase->oagpSupplier->name }}</td>
                                                <td>{{ $perOAGPPurchase->date }}</td>
                                                <td>
                                                    @php
                                                        $totalPayableAmountInformation = "<p>";
                                                        $totalPayableAmountInformation = $totalPayableAmountInformation.'<b>Total price :</b> '.$perOAGPPurchase->oagpTotalPrice()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $totalPayableAmountInformation = $totalPayableAmountInformation.'<b>Discount :</b> '.$perOAGPPurchase->discount.'% <br/>';
                                                        $totalPayableAmountInformation = $totalPayableAmountInformation."</p>";
                                                    @endphp

                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="date-range-popover" data-bs-title="Date range information" data-bs-content="{{ $totalPayableAmountInformation }}">
                                                        {{ $perOAGPPurchase->oagpPayableAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                                    </button>
                                                </td>
                                                <td>{{ $perOAGPPurchase->paid_amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                <td>{{ $perOAGPPurchase->oagpDueAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                <td>Detail | Edit</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">
                                                    <b class="d-flex justify-content-center text-warning">No purchase found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div id="dueNavTabPaginationDiv" class="mb-1">
                                {{ $dueOAGPPurchases->links() }}
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
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

            $(document).on('click', "#dueNavTabPaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#completeNavTabPaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#dueNavTab", function () {
                $("#selectedNavTabForSorting").val(null);
                $("#selectedNavTabForSorting").val("Due");
            });

            $(document).on('click', "#completeNavTab", function () {
                $("#selectedNavTabForSorting").val(null);
                $("#selectedNavTabForSorting").val("Complete");
            });

            $(document).on('change', "#paginationInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Pagination is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"paginationInputForSorting");
            });

            $(document).on('change', "#dateInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    if($("#dateConditionInputForSorting").val().length > 0){
                        dataTableLoad(parameterGenerate());
                    }
                }
                else{
                    errorMessages.push("Date status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"dateInputForSorting");
            });

            $(document).on('change', "#dateConditionInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Date condition status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"dateConditionInputForSorting");
            });


            $(document).on('click', "#searchButton", function () {
                var errorMessages = [];
                if($("#searchInputForSorting").val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Search is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"searchInputForSorting");
            });

        });

        function parameterGenerate(){
            var parameterString = null;
            $.each( [
                    "paginationInputForSorting",
                    "dateInputForSorting",
                    "dateConditionInputForSorting",
                    "searchInputForSorting",
                    "selectedNavTabForSorting"
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
            var routeUrl = "{{ route('oil.and.gas.pump.purchase.index',['oagpSlug'=> $oilAndGasPump->slug]) }}"

            $.ajax({
                type: "get",
                url: routeUrl + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");;

                    switch ($("#selectedNavTabForSorting").val()) {
                        case "Due":
                            $("#dueNavTabDiv").html($(result).find("#dueNavTabDiv").html());
                        break;

                        default:
                            $("#completeNavTabDiv").html($(result).find("#completeNavTabDiv").html());
                        break;
                    }
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

        function hideOrShowInputFieldErrorMessages(errorMessages,fieldId){
            if(errorMessages.length > 0){
                $("#"+fieldId+"ErrorMessageDiv").show();
                $("#"+fieldId+"ErrorMessageDiv").html("<ul></ul>");
                $( errorMessages).each(function( index,perError ) {
                    $("#"+fieldId+"ErrorMessageDiv"+" ul").append( "<li>"+perError+"</li>");
                });
            }
            else{
                $("#"+fieldId+"ErrorMessageDiv").hide();
                $("#"+fieldId+"ErrorMessageDiv").html("");
            }
        }
    </script>
@endpush


@push('onPageExtraCss')
    <style>
        .receivable-amount-popover {
            --bs-popover-max-width: auto;
            --bs-popover-border-color: var(--bs-primary);
            --bs-popover-header-bg: var(--bs-primary);
            --bs-popover-header-color: var(--bs-white);
            --bs-popover-body-padding-x: 1rem;
            --bs-popover-body-padding-y: .5rem;
        }

        .date-range-popover {
            --bs-popover-max-width: auto;
            --bs-popover-border-color: var(--bs-primary);
            --bs-popover-header-bg: var(--bs-primary);
            --bs-popover-header-color: var(--bs-white);
            --bs-popover-body-padding-x: 1rem;
            --bs-popover-body-padding-y: .5rem;
        }
    </style>
@endpush

