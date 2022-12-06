@extends('layouts.app')

@section('mainPageName')
    Project contract
@endsection

@section('mainCardTitle')
    Details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("project.contract.index") }}">Project contract</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    @php
        $investedAmount = $projectContract->invested_amount;

        $totalRevenueAmount = ($projectContract->journals->count() == 0 ) ? 0 : $projectContract->journals()->where("entry_type","Revenue")->sum("amount");
        $totalLossAmount = ($projectContract->journals->count() == 0 ) ? 0 : $projectContract->journals()->where("entry_type","Loss")->sum("amount");
        $totalReceivableAmount = ($investedAmount + $totalRevenueAmount) - $totalLossAmount;

    @endphp

    <div class="card border-dark mb-2">
        <h5 class="card-header">General information</h5>
        <div class="card-body text-dark mb-2">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Name</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $projectContract->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Code</th>
                                            <th>:</th>
                                            <td>
                                                {{ $projectContract->code }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Client</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $projectContract->client->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Category</th>
                                            <th>:</th>
                                            <td>{{ $projectContract->category->name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Etart date</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ( ($projectContract->start_date == null) ? "Not added." : date('d-M-Y',strtotime($projectContract->start_date)) ) }}</td>
                                        </tr>
                                        <tr>
                                            <th>End date</th>
                                            <th>:</th>
                                            <td>
                                                {{ ( ($projectContract->end_date == null) ? "Not added." : date('d-M-Y',strtotime($projectContract->end_date)) ) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%;">Status</th>
                                            <th style="width: 1%;">:</th>
                                            <td>
                                                <span class="badge p-2 @if($projectContract->status == "Ongoing") text-bg-primary @endif @if($projectContract->status == "Upcoming") text-bg-secondary @endif @if($projectContract->status == "Complete") text-bg-success @endif" style="font-size: 13px;"> {{ $projectContract->status }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Receivable status</th>
                                            <th>:</th>
                                            <td>
                                                <span class="badge p-2 @if($projectContract->receivable_status == "NotStarted") text-bg-primary @endif @if($projectContract->receivable_status == "Due") text-bg-warning @endif @if($projectContract->receivable_status == "Partial") text-bg-secondary @endif @if($projectContract->receivable_status == "Full") text-bg-success @endif" style="font-size: 13px;"> {{ ($projectContract->receivable_status == "NotStarted") ? "Not started" : $projectContract->receivable_status }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 35%;">Invested amount</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ $investedAmount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total revenue</th>
                                            <th>:</th>
                                            <td>
                                                {{ $totalRevenueAmount }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Total loss</th>
                                            <th>:</th>
                                            <td>
                                                {{ $totalLossAmount }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Receivable</th>
                                            <th>:</th>
                                            <td>
                                                {{ $totalReceivableAmount }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-body text-dark mb-2">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <b class="d-flex justify-content-center mb-1">
                                Description
                            </b>
                            <p>
                                {{ ($projectContract->description == null) ? "Not added." : $projectContract->description }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <b class="d-flex justify-content-center mb-1">
                                Note
                            </b>
                            <ul>
                                @forelse ($projectContract->note as $perNote)
                                    <li> {{ $perNote }}</li>
                                @empty
                                    <li> No note added.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-dark mb-3">
        <h5 class="card-header">System information</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Created at</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContract->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($projectContract->created_at))." at ".date('h:i:s a',strtotime($projectContract->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContract->created_by_id == null) ? "Not added yet." : $projectContract->createdBy->name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Update at</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContract->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($projectContract->updated_at))." at ".date('h:i:s a',strtotime($projectContract->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContract->updated_at == null) ? "Not updated yet." : (($projectContract->updatedBy() == null) ? "Unknown" : $projectContract->updatedBy()->name) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if(Auth::user()->hasUserPermission(["ACLMP01"]) == true)
        <div class="card border-dark mb-3">
            <h5 class="card-header">Activity Logs</h5>
            <div class="card-body">
                @php
                    $activitylogLimit = 3;
                @endphp
                <div class="card-body text-dark">
                    <div class="d-flex justify-content-center">
                        <p>
                            <b>Showing {{ ($activitylogLimit < $projectContract->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $projectContract->activityLogs()->count() }} activity log(s).</b>
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table class=" table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Event</th>
                                    <th>Causer</th>
                                    <th>Description</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projectContract->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
                                    <tr>
                                        <td>{{ $perIndex + 1 }}</td>
                                        <td>{{ Str::ucfirst($perActivityLogDatas->event) }}</td>
                                        <td>{{ ($perActivityLogDatas->causer == null) ? "Unknown": $perActivityLogDatas->causer->name }}</td>
                                        <td>{{ $perActivityLogDatas->description }}</td>
                                        <td>{{ ($perActivityLogDatas->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($perActivityLogDatas->created_at))." at ".date('h:i:s a',strtotime($perActivityLogDatas->created_at)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <b class="d-flex justify-content-center text-warning">No activity found.</b>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <div class="btn-group" role="group">
                    @if (Auth::user()->hasUserPermission(["PCMP04"]) == true)
                        <a href="{{ route("project.contract.edit",["slug"=>$projectContract->slug]) }}" class="btn btn-primary">Edit</a>
                    @endif

                    @if (!($projectContract->status == "Complete") && (Auth::user()->hasUserPermission(["PCMP05"]) == true))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                            Delete
                        </button>
                    @endif

                    @if (!($projectContract->status == "Upcomming") && (Auth::user()->hasUserPermission(["PCJMP01"]) == true))
                        <a href="{{ route("project.contract.journal.index",["pcSlug" => $projectContract->slug]) }}" class="btn btn-info">Journals</a>
                    @endif

                </div>
            </div>

            @if (!($projectContract->status == "Complete") && (Auth::user()->hasUserPermission(["PCMP05"]) == true))
                <div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $projectContract->name }} delete confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>Client will not show dependency.</li>
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("project.contract.delete",["slug" => $projectContract->slug]) }}" method="POST">
                                    @csrf
                                    @method("DELETE")
                                    <button type="submit" class="btn btn-sm btn-success">Yes,Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("project.contract.index") }}" class="btn btn-sm btn-secondary">
                    Go to project contract
                </a>
            </div>
        </div>
    </div>
@endsection

