@extends('layouts.app')

@section('mainPageName')
    Activity log
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item active" aria-current="page">Activity log</li>
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
                                    <label class="col-form-label col-form-label-sm col-md-3">Subject</label>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-sm" id="subjectTypeInputForSorting" name="subject_type">
                                            <option value="">Select a subject</option>
                                            @foreach ( $subjectTypes as $perSubjectType)
                                                <option value="{{ $perSubjectType }}">{{ $perSubjectType }}</option>
                                            @endforeach
                                        </select>
                                        <div id="subjectTypeInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-form-label col-form-label-sm col-md-3">Event</label>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-sm" id="eventInputForSorting" name="event">
                                            <option value="">Select a event</option>
                                            @foreach ( $events as $perLogEvent)
                                                <option value="{{ $perLogEvent }}">{{ $perLogEvent }}</option>
                                            @endforeach
                                        </select>
                                        <div id="eventInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-form-label col-form-label-sm col-md-3">Causer</label>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-sm" id="causerInputForSorting" name="causer">
                                            <option value="">Select a causer</option>
                                            <option value="All">All</option>
                                            @foreach ( $causers as $perCauser)
                                                <option value="{{ $perCauser->slug }}">{{ ($perCauser->slug == Auth::user()->slug) ? "Me" : $perCauser->name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="causerInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-form-label col-form-label-sm col-md-3">Created at </label>
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <input type="date" id="createdAtInputForSorting" name="created_at" class="form-control form-control-sm" >
                                            <select id="createdAtConditionInputForSorting" name="created_at_condition" class="form-control form-control-sm">
                                                @foreach ([""=>"Select a condition","="=>"Equal","<"=>"Less than",">"=>"Greater than","<="=>"Equal or less than",">="=>"Equal or greater than"] as $perFieldIndex => $perField)
                                                    <option value="{{ $perFieldIndex }}" {{ ($perFieldIndex=="EqualOrLessThan") ? "selected" : null }}>{{ $perField }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="createdAtInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
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
                                    <th>Log name</th>
                                    <th>Event</th>
                                    <th>Created at</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activitLogs as $activitLogIndex => $perActivitLog)
                                    <tr>
                                        <td>{{ $activitLogIndex + 1 }}</td>
                                        <td>{{ $perActivitLog->log_name }}</td>
                                        <td>{{ $perActivitLog->event }}</td>
                                        <td>{{ ($perActivitLog->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($perActivitLog->created_at))." at ".date('h:i:s a',strtotime($perActivitLog->created_at)) }}</td>
                                        <td>
                                            @if (Auth::user()->hasUserPermission(["ACLMP02"]) == true)
                                                <a href="{{ route("activity.log.details",["id" => $perActivitLog->id]) }}" class="btn btn-sm btn-info m-2"> Details</a>
                                            @endif

                                            @if (Auth::user()->hasUserPermission(["ACLMP03"]) == true)
                                                <button type="button" class="btn btn-sm btn-danger m-2" data-bs-toggle="modal" data-bs-target="#activityLogDeleteConfirmationModal{{ $perActivitLog->id }}">
                                                    Delete
                                                </button>
                                                <div class="modal fade" id="activityLogDeleteConfirmationModal{{ $perActivitLog->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ $perActivitLog->log_name }} delete confirmation modal</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>
                                                                    If you delete this you can not recover it. Are you sure you want to delete this?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                                                <form action="{{ route("activity.log.delete",["id" => $perActivitLog->id]) }}" method="POST">
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
                                        <td colspan="5">
                                            <b class="d-flex justify-content-center text-warning">No activity log found.</b>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <div id="paginationDiv" class="mb-1">
                        {{ $activitLogs->links() }}
                    </div>

                    @if (Auth::user()->hasUserPermission(["ACLMP04"]) == true)
                        <div class="mt-1">
                            <form action="{{ route("activity.log.delete.all.logs") }}" method="POST">
                                @csrf
                                @method("DELETE")
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="number" class="form-control" name="delete_records_older_than" placeholder="Enter older than days." min="1" maxlength="365" required>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllOlderActivityLogModal">Delete</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                    </div>
                                </div>

                                <div class="modal fade" id="deleteAllOlderActivityLogModal" tabindex="-1" >
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

            $(document).on('change', "#subjectTypeInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Subject type is empty.");
                }
                showFieldErrorMessages(errorMessages,"subjectTypeInputForSorting");
            });

            $(document).on('change', "#eventInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Event is empty.");
                }
                showFieldErrorMessages(errorMessages,"eventInputForSorting");
            });

            $(document).on('change', "#causerInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Causer is empty.");
                }
                showFieldErrorMessages(errorMessages,"causerInputForSorting");
            });

            $(document).on('change', "#createdAtInputForSorting, #createdAtConditionInputForSorting", function () {
                var errorMessages=[];
                if(($("#createdAtInputForSorting").val().length > 0)){
                    if(($("#createdAtConditionInputForSorting").val().length > 0)){
                        dataTableLoad(parameterGenerate());
                    }
                    else{
                        errorMessages.push("Created at condition is empty.");
                    }
                }
                else{
                    errorMessages.push("Created at is empty.");
                }
                showFieldErrorMessages(errorMessages,"createdAtInputForSorting");
            });
        });

        function parameterGenerate(){
            var parameterString = null;
            $.each( ["paginationInputForSorting","subjectTypeInputForSorting","eventInputForSorting","causerInputForSorting","createdAtInputForSorting","createdAtConditionInputForSorting"], function( key, perInput ) {
                if(($("#" + perInput).val().length>0)){
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
                url: "{{ route('activity.log.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#dataTableDiv").html($(result).find("#dataTableDiv").html());
                },
                error: function(errorResponse) {
                    errorMessages.push(errorResponse);
                    showApiErrorMessages(errorMessages);
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
