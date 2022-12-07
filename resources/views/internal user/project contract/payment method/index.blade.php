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
            <li class="breadcrumb-item">Project contract</li>
            <li class="breadcrumb-item active" aria-current="page">Payment method</li>
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
                    @if (Auth::user()->hasUserPermission(["PCPMMP02"]) == true)
                        <a href="{{ route("project.contract.payment.method.create") }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create record</a>
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
                        <div class="col-md-12 mb-2" hidden>
                            <input type="text" name="selected_nav_tab" id="selectedNavTabForSorting" class="form-control form-control-sm" readonly required value="Active" hidden>
                        </div>

                        <div class="row mb-2">
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

                            <div class="col-md-6 mb-2">
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
                        <button class="nav-link active" id="activeNavTab" data-bs-toggle="tab" data-bs-target="#activeNavTabDiv" type="button" role="tab">Active</button>
                        <button class="nav-link" id="trashNavTab" data-bs-toggle="tab" data-bs-target="#trashNavTabDiv" type="button" role="tab">Trash</button>
                    </div>
                </nav>
                <div class="tab-content" id="tabGroupDivContent">
                    <div class="tab-pane fade show active" id="activeNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($activeContractPaymentMethods as $perPaymentMethodIndex => $perPaymentMethod)
                                            <tr>
                                                <td>{{ $perPaymentMethodIndex+1 }}</td>
                                                <td>{{ $perPaymentMethod->name }}</td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["PCPMMP03"]) == true)
                                                        <a href="{{ route("project.contract.payment.method.details",["slug" => $perPaymentMethod->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCPMMP04"]) == true)
                                                        <a href="{{ route("project.contract.payment.method.edit",["slug" => $perPaymentMethod->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCPMMP05"]) == true)
                                                        <button type="button" class="btn btn-sm btn-danger m-1" data-bs-toggle="modal" data-bs-target="#{{$perPaymentMethod->slug}}TrashConfirmationModal">
                                                            Trash
                                                        </button>

                                                        <div class="modal fade" id="{{$perPaymentMethod->slug}}TrashConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perPaymentMethod->name }} delete confirmation.</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Payment method will not show dependency.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("project.contract.payment.method.trash",["slug" => $perPaymentMethod->slug]) }}" method="POST">
                                                                            @csrf
                                                                            @method("DELETE")
                                                                            <button type="submit" class="btn btn-sm btn-success">Yes,Trash</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3">
                                                    <b class="d-flex justify-content-center text-warning">No payment method found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div id="activeNavTabPaginationDiv" class="mb-1">
                                {{ $activeContractPaymentMethods->links() }}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="trashNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($trashContractPaymentMethods as $perPaymentMethodIndex => $perPaymentMethod)
                                            <tr>
                                                <td>{{ $perPaymentMethodIndex+1 }}</td>
                                                <td>{{ $perPaymentMethod->name }}</td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["PCPMMP03"]) == true)
                                                        <a href="{{ route("project.contract.payment.method.details",["slug" => $perPaymentMethod->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCPMMP04"]) == true)
                                                        <a href="{{ route("project.contract.payment.method.edit",["slug" => $perPaymentMethod->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCPMMP06"]) == true)
                                                        <button type="button" class="btn btn-sm btn-success m-1" data-bs-toggle="modal" data-bs-target="#{{$perPaymentMethod->slug}}RestoreConfirmationModal">
                                                            Restore
                                                        </button>
                                                        <div class="modal fade" id="{{$perPaymentMethod->slug}}RestoreConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perPaymentMethod->name }} restore confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Payment method will show dependency.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("project.contract.payment.method.restore",["slug" => $perPaymentMethod->slug]) }}" method="POST">
                                                                            @csrf
                                                                            @method("PATCH")
                                                                            <button type="submit" class="btn btn-sm btn-success">Yes,Restore</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3">
                                                    <b class="d-flex justify-content-center text-warning">No payment method found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div id="trashNavTabPaginationDiv" class="mb-1">
                                {{ $trashContractPaymentMethods->links() }}
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
            $(document).on('click', "#activeNavTabPaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#trashNavTabPaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#activeNavTab", function () {
                $("#selectedNavTabForSorting").val(null);
                $("#selectedNavTabForSorting").val("Active");
            });

            $(document).on('click', "#trashNavTab", function () {
                $("#selectedNavTabForSorting").val(null);
                $("#selectedNavTabForSorting").val("Trash");
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
            $.each( ["paginationInputForSorting","searchInputForSorting","selectedNavTabForSorting"], function( key, perInput ) {
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
                url: "{{ route('project.contract.payment.method.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");;

                    switch ($("#selectedNavTabForSorting").val()) {
                        case "Active":
                            $("#activeNavTabDiv").html($(result).find("#activeNavTabDiv").html());
                        break;

                        default:
                            $("#trashNavTabDiv").html($(result).find("#trashNavTabDiv").html());
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
