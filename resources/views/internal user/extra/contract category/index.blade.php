@extends('layouts.app')

@section('mainPageName')
    Contract category
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item active" aria-current="page">Contract category</li>
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
                    @if (Auth::user()->hasUserPermission(["CCMP02"]) == true)
                        <a href="{{ route("contract.category.create") }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create contract category</a>
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
                                    <label class="col-md-4 col-form-label col-form-label-sm">Contract category</label>
                                    <div class="col-md-8">
                                        <select class="form-control form-control-sm" id="contractCategoryTreeInputForSorting" name="contract_category">
                                            <option>Select</option>
                                            <option value="All">All</option>
                                            <x-contract_category.form.contract-categories :categories="$contractCategoriesTree" :activeCategorySlug="null"/>
                                        </select>
                                        <div id="contractCategoryTreeInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
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
                                            <th>Code</th>
                                            <th>Parent</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($activeContractCategories as $perCategoryIndex => $perCategory)
                                            <tr>
                                                <td>{{ $perCategoryIndex+1 }}</td>
                                                <td>{{ $perCategory->name }}</td>
                                                <td>{{ $perCategory->code }}</td>
                                                <td>{{ ($perCategory->parent_id == null) ? "Parent category" : $perCategory->parentCategory->name }}</td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["CCMP03"]) == true)
                                                        <a href="{{ route("contract.category.details",["slug" => $perCategory->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["CCMP04"]) == true)
                                                        <a href="{{ route("contract.category.edit",["slug" => $perCategory->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["CCMP05"]) == true)
                                                        <button type="button" class="btn btn-sm btn-danger m-1" data-bs-toggle="modal" data-bs-target="#{{$perCategory->slug}}TrashConfirmationModal">
                                                            Trash
                                                        </button>

                                                        <div class="modal fade" id="{{$perCategory->slug}}TrashConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perCategory->name }} delete confirmation.</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Contract category will not show in the tree except edit.</li>
                                                                                <li>All the sub contract category(ies) will not show in the tree except edit.</li>
                                                                                @foreach ($perCategory->dependencyNeedToTrashRecordsInfo() as $perDependency)
                                                                                    <li>{{ $perDependency }}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("contract.category.trash",["slug" => $perCategory->slug]) }}" method="POST">
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
                                                <td colspan="5">
                                                    <b class="d-flex justify-content-center text-warning">No contract category found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div id="activeNavTabPaginationDiv" class="mb-1">
                                {{ $activeContractCategories->links() }}
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
                                            <th>Code</th>
                                            <th>Parent</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($trashContractCategories as $perCategoryIndex => $perCategory)
                                            <tr>
                                                <td>{{ $perCategoryIndex+1 }}</td>
                                                <td>{{ $perCategory->name }}</td>
                                                <td>{{ $perCategory->code }}</td>
                                                <td>{{ ($perCategory->parent_id == null) ? "Parent category" : $perCategory->parentCategory->name }}</td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["CCMP03"]) == true)
                                                        <a href="{{ route("contract.category.details",["slug" => $perCategory->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["CCMP04"]) == true)
                                                        <a href="{{ route("contract.category.edit",["slug" => $perCategory->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["CCMP06"]) == true)
                                                        <button type="button" class="btn btn-sm btn-success m-1" data-bs-toggle="modal" data-bs-target="#{{$perCategory->slug}}RestoreConfirmationModal">
                                                            Restore
                                                        </button>
                                                        <div class="modal fade" id="{{$perCategory->slug}}RestoreConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perCategory->name }} restore confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Contract category will show in the tree.</li>
                                                                                <li>All the sub contract category(ies) will show in the tree.</li>
                                                                                @foreach ($perCategory->dependencyNeedToRestoreRecordsInfo() as $perDependency)
                                                                                    <li>{{ $perDependency }}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("contract.category.restore",["slug" => $perCategory->slug]) }}" method="POST">
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
                                                <td colspan="5">
                                                    <b class="d-flex justify-content-center text-warning">No contract category found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div id="trashNavTabPaginationDiv" class="mb-1">
                                {{ $trashContractCategories->links() }}
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

            $(document).on('change', "#contractCategoryTreeInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Contract category is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"contractCategoryTreeInputForSorting");
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
            $.each( ["paginationInputForSorting","contractCategoryTreeInputForSorting","searchInputForSorting","selectedNavTabForSorting"], function( key, perInput ) {
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
            var errorMessages = [];
            $.ajax({
                type: "get",
                url: "{{ route('contract.category.index') }}" + "?" + parameterString,
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
                    errorMessages.push(errorResponse);
                    showExtraErrorMessages(errorMessages);
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
