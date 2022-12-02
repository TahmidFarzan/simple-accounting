@extends('layouts.app')

@section('mainPageName')
    Activity log
@endsection

@section('mainCardTitle')
    Activity log details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("activity.log.index") }}">Activity log</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">
        <h5 class="card-header">General information</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card pt-3">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Log name</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ $activitLog->log_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Subject type</th>
                                            <th>:</th>
                                            <td> {{ str_replace("App\Models","",$activitLog->subject_type) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3" >
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: auto!important;">Event</th>
                                            <th style="width: 1%!important;">:</th>
                                            <td>{{ Str::ucfirst($activitLog->event) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Causer type</th>
                                            <th>:</th>
                                            <td> {{ ($activitLog->causer == null) ? "Unknown" : $activitLog->causer->name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card pt-3">
                        <div class="card-body">
                            <p class="d-flex justify-content-center">
                                <b>Description</b>
                            </p>
                            {{ $activitLog->description }}
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card pt-3">
                        <div class="card-body">
                            <div class="row">
                                @foreach ( json_decode($activitLog->properties) as $perPropertyIndex => $perProperty )
                                    <div class="col-md-6 {{ (Str::upper($perPropertyIndex) == "OLD") ? null : "mb-2" }}">
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="d-flex justify-content-center">
                                                    <b>{{ Str::ucfirst($perPropertyIndex) }}</b>
                                                </p>
                                                @if (str_replace("App\Models","",$activitLog->subject_type) == "\Setting")
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <tbody>
                                                                @foreach ($perProperty as $perPropertyRecordIndex => $perPropertyRecord)
                                                                    @if (Str::studly($perPropertyRecordIndex) == "FieldsWithValues")
                                                                        @foreach ( $perPropertyRecord as $perRecordIndex => $perRecordValue)
                                                                            <tr>
                                                                                <td>{{ Str::ucfirst(str_replace("_"," ",$perRecordIndex)) }}</td>
                                                                                <td>:</td>
                                                                                <td>{{ $perRecordValue }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif

                                                @if (!(str_replace("App\Models","",$activitLog->subject_type) == "\Setting"))
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <tbody>
                                                                @foreach ($perProperty as $perRecordIndex => $perRecord)
                                                                    <tr>
                                                                        <td>{{ Str::ucfirst(str_replace("_"," ",$perRecordIndex)) }}</td>
                                                                        <td>:</td>
                                                                        <td>
                                                                            @if (!($perRecordIndex == "updated_at"))
                                                                                {{ $perRecord }}
                                                                            @endif

                                                                            @if ($perRecordIndex == "updated_at")
                                                                                {{ ($perRecord == null) ? "Not added yet." : date('d-M-Y',strtotime($perRecord))." at ".date('h:i:s a',strtotime($perRecord)) }}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-dark mb-3">
        <h5 class="card-header">System information</h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 25%;">Created at</th>
                            <th style="width: 1%;">:</th>
                            <td>{{ ($activitLog->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($activitLog->created_at))." at ".date('h:i:s a',strtotime($activitLog->created_at)) }}</td>
                        </tr>
                        <tr>
                            <th style="width: 25%;">Update at</th>
                            <th style="width: 1%;">:</th>
                            <td>{{ ($activitLog->updated_at == null) ? "Not added yet." : date('d-M-Y',strtotime($activitLog->updated_at))." at ".date('h:i:s a',strtotime($activitLog->updated_at)) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if (Auth::user()->hasUserPermission(["ACLMP03"]) == true)
        <div class="card border-dark mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div class="btn-group" role="group" >
                        <button type="button" class="btn btn btn-danger" data-bs-toggle="modal" data-bs-target="#activityLogDeleteConfirmationModal">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="activityLogDeleteConfirmationModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $activitLog->log_name }} delete confirmation modal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            If you delete this you can not recover it. Are you sure you want to delete this?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <form action="{{ route("activity.log.delete",["id" => $activitLog->id]) }}" method="POST">
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="btn btn-success">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("activity.log.index") }}" class="btn btn-sm btn-secondary">
                    Go to activity log
                </a>
            </div>
        </div>
    </div>
@endsection
