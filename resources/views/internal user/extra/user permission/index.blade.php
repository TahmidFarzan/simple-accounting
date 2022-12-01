@extends('layouts.app')

@section('mainPageName')
    User permission
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
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#sortingCollapseDiv" aria-expanded="false" aria-controls="sortingCollapseDiv">
                        <i class="fa-solid fa-sort"></i> Sorting
                    </button>
                </p>
            </div>

            <div class="row mb-2" id="apiErrorDiv" style="display: none;"></div>

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
                                                <option value="">Select a pagination</option>
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
                                        <label class="col-md-4 col-form-label col-form-label-sm">Type</label>
                                        <div class="col-md-8 mb-2">
                                            <select class="form-control form-control-sm" id="typeInputForSorting" name="type">
                                                <option value="">Select a type</option>
                                                @foreach ( $types as $perType)
                                                    <option value="{{ $perType }}">{{ $perType }}</option>
                                                @endforeach
                                            </select>
                                            <div id="typeInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
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
                                    <th>type</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($userPermissions as $perUserPermissionIndex => $perUserPermission)
                                    <tr>
                                        <td>{{ $perUserPermissionIndex + 1 }}</td>
                                        <td>{{ $perUserPermission->name }}</td>
                                        <td>{{  Str::ucfirst(Str::lower(preg_replace("/([a-z])([A-Z])/", "$1 $2", $perUserPermission->type))) }}</td>
                                        <td>
                                            @if (Auth::user()->hasUserPermission(["UPMP02"]) == true)
                                                <a href="{{ route("user.permission.details",["slug" => $perUserPermission->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <b class="d-flex justify-content-center text-warning">No user permission found.</b>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="paginationDiv" class="mb-1">
                        {{ $userPermissions->links() }}
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
                showFieldErrorMessages(errorMessages,"paginationInputForSorting");
            });

            $(document).on('change', "#typeInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Type is empty.");
                }
                showFieldErrorMessages(errorMessages,"typeInputForSorting");
            });

            $(document).on('click', "#searchButtonForSorting", function () {
                var errorMessages=[];
                if($("#searchInputForSorting").val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Search is empty.");
                }
                showFieldErrorMessages(errorMessages,"searchInputForSorting");
            });
        });

        function parameterGenerate(){
            var parameterString=null;
            $.each( ["paginationInputForSorting","searchInputForSorting","typeInputForSorting"], function( key, perInput ) {
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
                url: "{{ route('user.permission.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#apiErrorDiv").hide();
                    $("#apiErrorDiv").html("");
                    $("#dataTableGridView").html($(result).find("#dataTableGridView").html());
                },
                error: function(errorResponse) {
                    showExtraErrorMessages(["Error " + errorResponse.status,errorResponse.statusText]);
                }
            });
        }

        function showApiErrorMessages(errorMessages){
            if(errorMessages.length>0){
                $("#apiErrorDiv").show();
                $("#apiErrorDiv").html('<div class="p-3"><div class="alert-messages alert alert-danger" role="alert"><div class="row"><div class="col-11 col-lg-11 col-md-11 col-sm-11" id="apiErrorMessageDiv"></div><div class="p-1 col-1 col-lg-1 col-md-1 col-sm-1"><button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div></div></div>');
                $("#apiErrorMessageDiv").html("");
                $("#apiErrorMessageDiv").html("<ul></ul>");
                $( errorMessages).each(function( index,perError ) {
                    $("#apiErrorMessageDiv ul").append( "<li>"+perError+"</li>");
                });
            }
            else{
                $("#apiErrorDiv").hide();
                $("#apiErrorDiv").html("");
                $("#apiErrorMessageDiv").html("");
            }
        }

        function showFieldErrorMessages(errorMessages,fieldId){
            if(errorMessages.length>0){
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
