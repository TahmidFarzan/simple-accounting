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
            <li class="breadcrumb-item"><a href="{{ route("project.contract.details",["slug" => $projectContract->slug]) }}">{{ $projectContract->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Journal</li>
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
                    @if (($projectContract->status == "Ongoing") || (Auth::user()->hasUserPermission(["PCJMP02"]) == true))
                        <a href="{{ route("project.contract.journal.create",["pcSlug" => $projectContract->slug]) }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create record</a>
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
                            <input type="text" name="selected_nav_tab" id="selectedNavTabForSorting" class="form-control form-control-sm" readonly required value="Revenue" hidden>
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
                                    <label class="col-md-4 col-form-label col-form-label-sm">Entry date</label>
                                    <div class="col-md-8">
                                        <div class="input-group mb-3">
                                            <input type="date" class="form-control form-control-sm" id="entryDateInputForSorting" name="entry_date">
                                            <select id="entryDateConditionInputForSorting" name="entry_date_condition" class="form-control form-control-sm">
                                                @foreach (array(""=>"Select","="=>"Equal","<"=>"Less than",">"=>"Greater than","<="=>"Equal or less than",">="=>"Equal or greater than") as $perConditionIndex => $perCondition)
                                                    <option value="{{ $perConditionIndex }}">{{ $perCondition }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="entryDateInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                        <div id="entryDateConditionInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
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
                        <button class="nav-link active" id="revenueNavTab" data-bs-toggle="tab" data-bs-target="#revenueNavTabDiv" type="button" role="tab">Revenue</button>
                        <button class="nav-link" id="lossNavTab" data-bs-toggle="tab" data-bs-target="#lossNavTabDiv" type="button" role="tab">Loss</button>
                    </div>
                </nav>
                <div class="tab-content" id="tabGroupDivContent">
                    <div class="tab-pane fade show active" id="revenueNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Entry date</th>
                                            <th>Amount ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($projectContractJournalRevenueEntries as $perProjectContractJournalIndex => $perProjectContractJournal)
                                            <tr>
                                                <td>{{ $perProjectContractJournalIndex + 1 }}</td>
                                                <td>{{ $perProjectContractJournal->name }}</td>
                                                <td>{{  ( ($perProjectContractJournal->entry_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContractJournal->entry_date)) )}} </td>
                                                <td> {{ $perProjectContractJournal->amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["PCJMP03"]) == true)
                                                        <a href="{{ route("project.contract.journal.details",["pcSlug" => $projectContract->slug,"slug" => $perProjectContractJournal->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if (($projectContract->status =="Ongoing") &&  (Auth::user()->hasUserPermission(["PCJMP04"]) == true))
                                                        <a href="{{ route("project.contract.journal.edit",["pcSlug" => $projectContract->slug,"slug" => $perProjectContractJournal->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @if (($projectContract->status =="Ongoing") && (Auth::user()->hasUserPermission(["PCJMP05"]) == true))
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#ongoingDeleteConfirmationModal">
                                                            Delete
                                                        </button>

                                                        <div class="modal fade" id="ongoingDeleteConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perProjectContractJournal->name }} delete confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Project contract will not show dependency.</li>
                                                                                <li>Can not recover record.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("project.contract.journal.delete",["pcSlug" => $projectContract->slug,"slug" => $perProjectContractJournal->slug]) }}" method="POST">
                                                                            @csrf
                                                                            @method("DELETE")
                                                                            <button type="submit" class="btn btn-sm btn-success">Yes,Delete</button>
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
                                                <td colspan="5">
                                                    <b class="d-flex justify-content-center text-warning">No journal entry found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div id="revenueNavTabPaginationDiv" class="mb-1">
                                {{ $projectContractJournalRevenueEntries->links() }}
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="lossNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Entry date</th>
                                            <th>Amount ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($projectContractJournalLossEntries as $perProjectContractJournalIndex => $perProjectContractJournal)
                                            <tr>
                                                <td>{{ $perProjectContractJournalIndex + 1 }}</td>
                                                <td>{{ $perProjectContractJournal->name }}</td>
                                                <td>{{  ( ($perProjectContractJournal->entry_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContractJournal->entry_date)) )}} </td>
                                                <td> {{ $perProjectContractJournal->amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["PCJMP03"]) == true)
                                                        <a href="{{ route("project.contract.journal.details",["pcSlug" => $projectContract->slug,"slug" => $perProjectContractJournal->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if (($projectContract->status =="Ongoing") &&  (Auth::user()->hasUserPermission(["PCJMP04"]) == true))
                                                        <a href="{{ route("project.contract.journal.edit",["pcSlug" => $projectContract->slug,"slug" => $perProjectContractJournal->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @if (($projectContract->status =="Ongoing") && (Auth::user()->hasUserPermission(["PCJMP05"]) == true))
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#ongoingDeleteConfirmationModal">
                                                            Delete
                                                        </button>
                                                        <div class="modal fade" id="ongoingDeleteConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perProjectContractJournal->name }} delete confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Project contract will not show dependency.</li>
                                                                                <li>Can not recover record.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("project.contract.journal.delete",["pcSlug" => $projectContract->slug,"slug" => $perProjectContractJournal->slug]) }}" method="POST">
                                                                            @csrf
                                                                            @method("DELETE")
                                                                            <button type="submit" class="btn btn-sm btn-success">Yes,Delete</button>
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
                                                <td colspan="5">
                                                    <b class="d-flex justify-content-center text-warning">No jornal entry found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div id="lossNavTabPaginationDiv" class="mb-1">
                                {{ $projectContractJournalLossEntries->links() }}
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
            $(document).on('click', "#revenueNavTabPaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#lossNavTabPaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#revenueNavTab", function () {
                $("#selectedNavTabForSorting").val(null);
                $("#selectedNavTabForSorting").val("Revenue");
            });

            $(document).on('click', "#lossNavTab", function () {
                $("#selectedNavTabForSorting").val(null);
                $("#selectedNavTabForSorting").val("Loss");
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

            $(document).on('change', "#entryDateInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    if($("#entryDateConditionInputForSorting").val().length > 0){
                        dataTableLoad(parameterGenerate());
                    }
                }
                else{
                    errorMessages.push("Entry date status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"entryDateInputForSorting");
            });

            $(document).on('change', "#entryDateConditionInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Entry date condition status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"entryDateConditionInputForSorting");
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
                    "entryDateInputForSorting",
                    "entryDateConditionInputForSorting",
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
            $.ajax({
                type: "get",
                url: "{{ route('project.contract.journal.index',['pcSlug' => $projectContract->slug]) }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");;

                    switch ($("#selectedNavTabForSorting").val()) {
                        case "Loss":
                            $("#lossNavTabDiv").html($(result).find("#lossNavTabDiv").html());
                        break;

                        default:
                            $("#revenueNavTabDiv").html($(result).find("#revenueNavTabDiv").html());
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



