@extends('layouts.app')

@section('mainPageName')
    Authentication log
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item active" aria-current="page">Authentication log</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">


        <div class="card-body text-dark">
            <div class="row mb-2">
                <p>
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#dataTableSortingCollapseDiv" aria-expanded="false" aria-controls="dataTableSortingCollapseDiv">
                        <i class="fa-solid fa-sort"></i> Sorting
                    </button>
                </p>
            </div>

            <div class="row mb-2" id="apiErrorDiv" style="display: none;"></div>

            <div class="row mb-2">
                <div class="collapse mb-2" id="dataTableSortingCollapseDiv">
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-form-label col-form-label-sm col-md-3">Pagination</label>
                                    <div class="col-md-9">
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
                                    <label class="col-form-label col-form-label-sm col-md-3">Login status</label>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-sm" id="loginStatusInputForSorting" name="login_status">
                                            @foreach (array(""=>"Select log in status","All" =>"All","1" =>"Yes","0" =>"No") as $perLoginStatusIndex => $perLoginStatus)
                                                <option value="{{ $perLoginStatusIndex }}">{{ $perLoginStatus }}</option>
                                            @endforeach
                                        </select>
                                        <div id="loginStatusInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-form-label col-form-label-sm col-md-3">User</label>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-sm" id="userInputForSorting" name="user">
                                            <option value="">Select a user</option>
                                            <option value="All">All</option>
                                            @foreach ( $users as $perUser)
                                                <option value="{{ $perUser->slug }}">{{ ($perUser->slug == Auth::user()->slug) ? "Me" : $perUser->name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="userInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-form-label col-form-label-sm col-md-3">Date</label>
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <select id="dateTypeInputForSorting" name="date_type" class="form-control form-control-sm">
                                                @foreach ([""=>"Select a date type","all"=>"All","login_at"=>"Login at","logout_at"=>"Logout at"] as $perFieldIndex => $perField)
                                                    <option value="{{ $perFieldIndex }}" {{ ($perFieldIndex=="all") ? "sselected" : null }}>{{ $perField }}</option>
                                                @endforeach
                                            </select>
                                            <input type="date" id="dateInputForSorting" name="date" class="form-control form-control-sm">
                                            <select id="dateConditionInputForSorting" name="date_condition" class="form-control form-control-sm">
                                                @foreach ([""=>"Select a condition","="=>"Equal","<"=>"Less than",">"=>"Greater than",">="=>"Equal or less than","<="=>"Equal or greater than"] as $perFieldIndex => $perField)
                                                    <option value="{{ $perFieldIndex }}">{{ $perField }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="dateInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="dataTableDiv">
                <div class="col-md-12">
                    <div class="table-responsive mb-2">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>User</th>
                                    <th>Ip address</th>
                                    <th>Login at</th>
                                    <th>Login successfull</th>
                                    <th>Logout</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($authenticationLogs as $authenticationLogIndex => $perAuthenticationLog)
                                    <tr>
                                        <td>{{ $authenticationLogIndex+1 }}</td>
                                        <td>
                                            @php
                                                $authUser = App\Models\User::withTrashed()->where("id",$perAuthenticationLog->authenticatable_id)->first();
                                            @endphp
                                            {{ ($authUser) ? ( ($authUser->id == Auth::user()->id) ? "Me": $authUser->name) : "Unknown" }}
                                        </td>
                                        <td>{{ $perAuthenticationLog->ip_address }}</td>
                                        <td>{{ ($perAuthenticationLog->login_at == null) ? "Not login yet." : date('d-M-Y',strtotime($perAuthenticationLog->login_at))." at ".date('h:i:s a',strtotime($perAuthenticationLog->login_at)) }}</td>
                                        <td>
                                            @if ($perAuthenticationLog->login_successful == 1)
                                                <span class="badge text-bg-success p-2">Yes</span>
                                            @endif

                                            @if ($perAuthenticationLog->login_successful == 0)
                                                <span class="badge text-bg-success p-2">No</span>
                                            @endif
                                        </td>
                                        <td>{{ ($perAuthenticationLog->logout_at == null) ? "Not logout yet." : date('d-M-Y',strtotime($perAuthenticationLog->logout_at))." at ".date('h:i:s a',strtotime($perAuthenticationLog->logout_at)) }}</td>
                                        <td>
                                            @if (Auth::user()->hasUserPermission(["ACLMP02"]) == true)
                                                <a href="{{ route("authentication.log.details",["id" => $perAuthenticationLog->id]) }}" class="btn btn-sm btn-info m-2"> Details</a>
                                            @endif

                                            @if (Auth::user()->hasUserPermission(["ACLMP03"]) == true)
                                                <button type="button" class="btn btn-sm btn btn-danger m-2" data-bs-toggle="modal" data-bs-target="#authenticationLogDeleteConfirmationModal{{ $perAuthenticationLog->id }}">
                                                    Delete
                                                </button>
                                                <div class="modal fade" id="authenticationLogDeleteConfirmationModal{{ $perAuthenticationLog->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ $perAuthenticationLog->log_name }} delete confirmation modal</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>
                                                                    If you delete this you can not recover it. Are you sure you want to delete this?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                                                <form action="{{ route("authentication.log.delete",["id" => $perAuthenticationLog->id]) }}" method="POST">
                                                                    @csrf
                                                                    @method("DELETE")
                                                                    <button type="submit" class="btn btn-success">Yes</button>
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
                                        <td colspan="6">
                                            <b class="d-flex justify-content-center text-warning">No authentication log found.</b>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <div id="paginationDiv" class="mb-1">
                        {{ $authenticationLogs->links() }}
                    </div>

                    @if (Auth::user()->hasUserPermission(["ACLMP04"]) == true)
                        <div class="mt-1">
                            <form action="{{ route("authentication.log.delete.all.logs") }}" method="POST">
                                @csrf
                                @method("DELETE")
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="number" class="form-control" name="delete_records_older_than" placeholder="Enter older than days." min="1" maxlength="365" required>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllOlderAuthenticationLogModal">Delete</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                    </div>
                                </div>

                                <div class="modal fade" id="deleteAllOlderAuthenticationLogModal" tabindex="-1" >
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete all older record confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    If you delete this you can not recover it. Are you sure you want to delete this?
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                                <button type="subnit" class="btn btn-success">Yes, Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
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
                parameterString = sortingParameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('change', "#paginationInputForSorting", function () {
                var errorMessages = [];
                if(($(this).val().length > 0)){
                    dataTableLoad(sortingParameterGenerate());
                }
                else{
                    errorMessages.push("Pagination is empty.");
                }
                showFieldErrorMessages(errorMessages,"paginationInputForSorting");
            });

            $(document).on('change', "#loginStatusInputForSorting", function () {
                var errorMessages = [];
                if(($(this).val().length > 0)){
                    dataTableLoad(sortingParameterGenerate());
                }
                else{
                    errorMessages.push("Login status is empty.");
                }
                showFieldErrorMessages(errorMessages,"loginStatusInputForSorting");
            });

            $(document).on('change', "#userInputForSorting", function () {
                var errorMessages = [];
                if(($(this).val().length > 0)){
                    dataTableLoad(sortingParameterGenerate());
                }
                else{
                    errorMessages.push("User is empty.");
                }
                showFieldErrorMessages(errorMessages,"userInputForSorting");
            });

            $(document).on('change', "#dateTypeInputForSorting, #dateInputForSorting, #dateConditionInputForSorting", function () {
                var errorMessages = [];
                if(($("#dateConditionInputForSorting").val().length > 0) && ($("#dateTypeInputForSorting").val().length > 0) && ($("#dateInputForSorting").val().length > 0)){
                    dataTableLoad(sortingParameterGenerate());
                }
                else{
                    if($("#dateTypeInputForSorting").val().length == 0){
                        errorMessages.push("Date type is empty.");
                    }

                    if($("#dateInputForSorting").val().length == 0){
                        errorMessages.push("Date type is empty.");
                    }

                    if($("#dateConditionInputForSorting").val().length == 0){
                        errorMessages.push("Date type is empty.");
                    }
                }

                showFieldErrorMessages(errorMessages,"dateInputForSorting");
            });

        });

        function sortingParameterGenerate(){
            var parameterString = null;
            $.each( ["paginationInputForSorting","loginStatusInputForSorting","userInputForSorting","dateInputForSorting","dateTypeInputForSorting","dateConditionInputForSorting"], function( key, perInput ) {
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
                url: "{{ route('authentication.log.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#dataTableDiv").html($(result).find("#dataTableDiv").html());
                },
                error: function(errorResponse) {
                    showExtraErrorMessages(["Error " + errorResponse.status,errorResponse.statusText]);
                }
            });
        }

        function showApiErrorMessages(errorMessages){
            if(errorMessages.length > 0){
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
