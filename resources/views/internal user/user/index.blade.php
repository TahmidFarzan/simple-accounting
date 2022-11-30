@extends('layouts.app')

@section('mainPageName')
    User
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item active" aria-current="page">User</li>
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
                    @if (Auth::user()->hasUserPermission(["UMP02","UMP03"]) == true)
                        <a href="{{ route("user.create") }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create user</a>
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
                                <input type="text" name="selected_nav_tab" id="selectedNavTabForSorting" class="form-control form-control-sm" readonly required value="Active" hidden>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm">Pagination</label>
                                        <div class="col-md-8 mb-2">
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
                                                <select class="form-control form-control-sm" id="searchFieldForSorting" name="search_field">
                                                    <option value="">Please select a field</option>
                                                    @foreach ($searchFields as  $perField)
                                                        <option value="{{ $perField }}">{{ $perField }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-sm btn-outline-primary" type="button" id="searchButtonForSorting">Go</button>
                                            </div>
                                            <div id="searchInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm">User role</label>
                                        <div class="col-md-8 mb-2">
                                            <select class="form-control form-control-sm" id="userRoleInputForSorting" name="user_role">
                                                <option value="">Select</option>
                                                @foreach ( array("All",'Owner','Subordinate') as $perUserRole)
                                                    <option value="{{ $perUserRole }}">{{ $perUserRole }}</option>
                                                @endforeach
                                            </select>
                                            <div id="userRoleInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <nav>
                    <div class="nav nav-tabs" id="navTabGroup" role="tablist">
                        <button class="nav-link active" id="activeNavTab" data-bs-toggle="tab" data-bs-target="#activeNavTabDiv" type="button" role="tab">Active</button>
                        <button class="nav-link" id="trashNavTab" data-bs-toggle="tab" data-bs-target="#trashNavTabDiv" type="button" role="tab">Trash</button>
                    </div>
                </nav>
                <div class="tab-content" id="navTabGroupDivContent">
                    <div class="tab-pane fade show active" id="activeNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>User role</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($activeUsers as $perUserIndex => $perUser)
                                            <tr>
                                                <td>{{ $perUserIndex + 1 }}</td>
                                                <td>{{ $perUser->name }}</td>
                                                <td>{{ $perUser->email }}</td>
                                                <td>{{ $perUser->user_role }}</td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["UMP04"]) == true)
                                                        <a href="{{ route("user.details",["slug" => $perUser->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if ((Auth::user()->hasUserPermission(["UMP05","UMP06"]) == true) && !(Auth::user()->id == $perUser->id))
                                                        <a href="{{ route("user.edit",["slug" => $perUser->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @php
                                                        $trashAableUser = false;
                                                        if(($perUser->user_role == "Owner") && (Auth::user()->hasUserPermission(["UMP07"]) == true)){
                                                            $trashAableUser = true;
                                                        }

                                                        if(($perUser->user_role == "Subordinate") && (Auth::user()->hasUserPermission(["UMP08"]) == true)){
                                                            $trashAableUser = true;
                                                        }
                                                    @endphp

                                                    @if (($trashAableUser == true) && !(Auth::user()->id == $perUser->id))
                                                        <button type="button" class="btn btn-sm btn-danger m-1" data-bs-toggle="modal" data-bs-target="#trash{{$perUser->slug}}ConfirmationModal">
                                                            Trash
                                                        </button>
                                                        <div class="modal fade" id="trash{{$perUser->slug}}ConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perUser->name }} trash confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>User will not show.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("user.trash",["slug" => $perUser->slug]) }}" method="POST">
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
                                                    <b class="d-flex justify-content-center text-warning">No user found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div id="activePaginationDiv" class="mb-1">
                                {{ $activeUsers->links() }}
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
                                            <th>Email</th>
                                            <th>User role</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($trashUsers as $perUserIndex => $perUser)
                                            <tr>
                                                <td>{{ $perUserIndex + 1 }}</td>
                                                <td>{{ $perUser->name }}</td>
                                                <td>{{ $perUser->email }}</td>
                                                <td>{{ $perUser->user_role }}</td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["UMP04"]) == true)
                                                        <a href="{{ route("user.details",["slug" => $perUser->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if ((Auth::user()->hasUserPermission(["UMP05","UMP06"]) == true) && !(Auth::user()->id == $perUser->id))
                                                        <a href="{{ route("user.edit",["slug" => $perUser->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @php
                                                        $restoreAableUser = false;
                                                        if(($perUser->user_role == "Owner") && (Auth::user()->hasUserPermission(["UMP09"]) == true)){
                                                            $restoreAableUser = true;
                                                        }

                                                        if(($perUser->user_role == "Subordinate") && (Auth::user()->hasUserPermission(["UMP10"]) == true)){
                                                            $restoreAableUser = true;
                                                        }
                                                    @endphp

                                                    @if (( $restoreAableUser == true) && !(Auth::user()->id == $perUser->id))
                                                        <button type="button" class="btn btn-sm btn-success m-1" data-bs-toggle="modal" data-bs-target="#restore{{$perUser->slug}}ConfirmationModal">
                                                            Restore
                                                        </button>
                                                        <div class="modal fade" id="restore{{$perUser->slug}}ConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perUser->name }} restore confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>User will show.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("user.restore",["slug" => $perUser->slug]) }}" method="POST">
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
                                                    <b class="d-flex justify-content-center text-warning">No user found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div id="trashPaginationDiv" class="mb-1">
                                {{ $trashUsers->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <h5 class="card-title">Dark card title 2</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
        </div>
    </div>
@endsection

@push("onPageExtraScript")
    <script>
        $(document).ready(function(){
            $(document).on('click', "#activePaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#trashPaginationDiv .pagination .page-item a", function () {
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


            $(document).on('change', "#userRoleInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("User role is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"searchInputForSorting");
            });

            $(document).on('click', "#searchButtonForSorting", function () {
                var errorMessages = [];
                if(($("#searchInputForSorting").val().length > 0) && ($("#searchFieldForSorting").val().length > 0)){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    if($("#searchInputForSorting").val().length == 0){
                        errorMessages.push("Search is empty.");
                    }

                    if($("#searchFieldForSorting").val().length == 0){
                        errorMessages.push("Search field is empty.");
                    }
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"searchInputForSorting");
            });
        });

        function parameterGenerate(){
            var parameterString=null;
            $.each( ["paginationInputForSorting","userRoleInputForSorting","searchInputForSorting","searchFieldForSorting","selectedNavTabForSorting"], function( key, perInput ) {
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
                url: "{{ route('user.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");

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
                    showExtraErrorMessages(errorResponse);
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
