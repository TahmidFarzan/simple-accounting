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
            <li class="breadcrumb-item active" aria-current="page">Project contract journal</li>
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
                        <label class="col-md-4 col-form-label col-form-label-sm">Entry date</label>
                        <div class="col-md-8">
                            <div class="input-group mb-3">
                                <input type="date" class="form-control form-control-sm" id="entryDateInputForGenerateReport" name="entry_date">
                                <select id="entryDateConditionInputForGenerateReport" name="entry_date_condition" class="form-control form-control-sm">
                                    @foreach (array(""=>"Select","="=>"Equal","<"=>"Less than",">"=>"Greater than","<="=>"Equal or less than",">="=>"Equal or greater than") as $perConditionIndex => $perCondition)
                                        <option value="{{ $perConditionIndex }}">{{ $perCondition }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="entryDateInputForGenerateReportErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                            <div id="entryDateConditionInputForGenerateReportErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Entry type</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="entryTypeInputForGenerateReport" name="entry_type">
                                <option value="">Select</option>
                                <option value="All">All</option>
                                @foreach ( $entryTypes as $perStatus)
                                    <option value="{{ $perStatus }}">{{ $perStatus }}</option>
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
            @if ($projectContractJournalEntries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Entry date</th>
                                <th>Entry Type</th>
                                <th>Description</th>
                                <th>Note</th>
                                <th>Amount ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                <th>Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projectContractJournalEntries as $perProjectContractJournalEntryIndex => $perProjectContractJournalEntry)
                                <tr>
                                    <td>{{ $perProjectContractJournalEntryIndex +1 }}</td>
                                    <td>{{ $perProjectContractJournalEntry->name }}</td>
                                    <td>{{date('d-M-Y',strtotime($perProjectContractJournalEntry->entry_date)) }} </td>
                                    <td><span class="badge p-2 @if($perProjectContractJournalEntry->entry_type == "Loss") text-bg-warning @endif @if($perProjectContractJournalEntry->entry_type == "Revenue") text-bg-success @endif" style="font-size: 13px;"> {{ $perProjectContractJournalEntry->entry_type }}</span></td>
                                    <td>{{($perProjectContractJournalEntry->description == null) ? "Not added yet." : $perProjectContractJournalEntry->description }}</td>
                                    <td>
                                        <ul>
                                            @forelse ($perProjectContractJournalEntry->note as $perNote)
                                                <li>{{ $perNote }}</li>
                                            @empty
                                                <li><b class="d-flex justify-content-center text-warning">Not added.</b></li>
                                            @endforelse
                                        </ul>
                                    </td>
                                    <td>{{ $perProjectContractJournalEntry->amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                    <td><a href="{{ route("report.project.contract.journal.details",["slug" => $perProjectContractJournalEntry->slug]) }}" class="btn btn-sm btn-info m-1">Details</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <b class="d-flex justify-content-center text-warning">No project contract journal found.</b>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="paginationDiv" class="mb-1">
                    {{ $projectContractJournalEntries->links() }}
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
                    "entryDateInputForGenerateReport",
                    "entryDateConditionInputForGenerateReport",
                    "searchInputForGenerateReport",
                    "entryTypeInputForGenerateReport",
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
                url: "{{ route('report.project.contract.journal.index') }}" + "?" + parameterString,
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
