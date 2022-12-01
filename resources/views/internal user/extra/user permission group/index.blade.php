@extends('layouts.app')

@section('mainPageName')
    User permission group
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
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
                    @if (Auth::user()->hasUserPermission(["UPGMP02"]) == true)
                        <a href="{{ route("user.permission.group.create") }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create</a>
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
                                        <label class="col-md-4 col-form-label col-form-label-sm">User permission</label>
                                        <div class="col-md-8 mb-2">
                                            <select class="form-control form-control-sm" id="userPermissionInputForSorting" name="user_permission">
                                                <option value="">Select</option>
                                                @foreach ( $userPermissions as $perUserPermission)
                                                    <option value="{{ $perUserPermission->slug }}">{{ $perUserPermission->name }}</option>
                                                @endforeach
                                            </select>
                                            <div id="userPermissionInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm">Search</label>
                                        <div class="col-md-8">
                                            <div class="input-group mb-3">
                                                <input type="search" class="form-control form-control-sm" placeholder="Search value." name="search" id="searchInputForSorting">
                                                <button class="btn btn-sm btn-outline-primary" type="button" id="searchButtonForSorting">Go</button>
                                            </div>
                                            <div id="searchInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="card-body" id="dataTableGridView">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($userPermissionGroups as $perUserPermissionGroupIndex => $perUserPermissionGroup)
                                    <tr>
                                        <td>{{ $perUserPermissionGroupIndex + 1 }}</td>
                                        <td>{{ $perUserPermissionGroup->name }}</td>
                                        <td>{{ $perUserPermissionGroup->code}}</td>
                                        <td>
                                            @if (Auth::user()->hasUserPermission(["UPGMP03"]) == true)
                                                <a href="{{ route("user.permission.group.details",["slug" => $perUserPermissionGroup->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                            @endif

                                            @if (Auth::user()->hasUserPermission(["UPGMP04"]) == true)
                                                <a href="{{ route("user.permission.group.edit",["slug" => $perUserPermissionGroup->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                            @endif

                                            @if (Auth::user()->hasUserPermission(["UPGMP05"]) == true)
                                                <button type="button" class="btn btn-sm btn-danger m-1" data-bs-toggle="modal" data-bs-target="#delete{{$perUserPermissionGroup->slug}}ConfirmationModal">
                                                    Delete
                                                </button>
                                                <div class="modal fade" id="delete{{$perUserPermissionGroup->slug}}ConfirmationModal" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5">{{ $perUserPermissionGroup->name }} delete confirmation model</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>
                                                                    <ul>
                                                                        <li>This record will be deleted.</li>
                                                                        <li>Recovery of this record is not possible.</li>
                                                                        <li>Dependency relatioon between user permissions which and this user permission group will be deleted.</li>
                                                                        <li> Recovery of dependency relatioon between user permissions which and this user permission group is not possible.</li>
                                                                    </ul>
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                <form action="{{ route("user.permission.group.delete",["slug" => $perUserPermissionGroup->slug]) }}" method="POST">
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
                                        <td colspan="4">
                                            <b class="d-flex justify-content-center text-warning">No user permission group found.</b>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="paginationDiv" class="mb-1">
                        {{ $userPermissionGroups->links() }}
                    </div>
                </div>
            </div>
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

            $(document).on('change', "#userPermissionInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Type is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"userPermissionInputForSorting");
            });

            $(document).on('click', "#searchButtonForSorting", function () {
                var errorMessages=[];
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
            var parameterString=null;
            $.each( ["paginationInputForSorting","searchInputForSorting","userPermissionInputForSorting"], function( key, perInput ) {
                if(($("#" + perInput).val().length>0)){
                    var inputFieldValue = $("#" + perInput).val();
                    var inputFieldName = $("#" + perInput).attr('name');
                    var curentParameterString = inputFieldName + "=" + inputFieldValue;
                    parameterString = (parameterString == null) ? curentParameterString : parameterString + "&" + curentParameterString;
                }
            });
            return parameterString;
        }

        function dataTableLoad(parameterString){
            $.ajax({
                type: "get",
                url: "{{ route('user.permission.group.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");
                    $("#dataTableGridView").html($(result).find("#dataTableGridView").html());
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
