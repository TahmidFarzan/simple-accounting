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
            <li class="breadcrumb-item"><a href="{{ route("project.contract.details",["slug" => $projectContract->slug]) }}">{{ $projectContract->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("project.contract.payment.index",["pcSlug" => $projectContract->slug]) }}">Payment</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')

    @php
        $passProjectContactValidation = false;

        if(($projectContract->status == "Complete") && !($projectContract->receivable_status == "Complete")){
            $passProjectContactValidation = true;
        }
        else{
            $passProjectContactValidation = false;
        }
    @endphp
    <div class="card border-dark mb-2">
        <h5 class="card-header">General information</h5>
        <div class="card-body text-dark mb-2">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <th style="width: 25%;">Name</th>
                            <th style="width: 1%;">:</th>
                            <td>{{ $projectContractPayment->name }}</td>
                        </tr>
                        <tr>
                            <th>Entry date</th>
                            <th>:</th>
                            <td>
                                {{ date('d-M-Y',strtotime($projectContractPayment->payment_date))." at ".date('h:i:s a',strtotime($projectContractPayment->payment_date)) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Payment method</th>
                            <th>:</th>
                            <td>{{ $projectContractPayment->paymentMethod->name }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th>:</th>
                            <td>{{ $projectContractPayment->amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                        </tr>
                    </tbody>
                </table>
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
                                {{ ($projectContractPayment->description == null) ? "Not added." : $projectContractPayment->description }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <b class="d-flex justify-content-center mb-1">
                                Note
                            </b>

                            <ul>
                                @forelse ($projectContractPayment->note as  $perNote )
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
                                            <td>{{ ($projectContractPayment->created_at == null) ? "Not added yet." : date('d-M-Y',strtotime($projectContractPayment->created_at))." at ".date('h:i:s a',strtotime($projectContractPayment->created_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Created by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContractPayment->created_by_id == null) ? "Not added yet." : $projectContractPayment->createdBy->name }}</td>
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
                                            <td>{{ ($projectContractPayment->updated_at == null) ? "Not updated yet." : date('d-M-Y',strtotime($projectContractPayment->updated_at))." at ".date('h:i:s a',strtotime($projectContractPayment->updated_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 25%;">Update by</th>
                                            <th style="width: 1%;">:</th>
                                            <td>{{ ($projectContractPayment->updated_at == null) ? "Not updated yet." : (($projectContractPayment->updatedBy() == null) ? "Unknown" : $projectContractPayment->updatedBy()->name) }}</td>
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
                            <b>Showing {{ ($activitylogLimit < $projectContractPayment->activityLogs()->count() ) ? "last ".$activitylogLimit : "All" }} out of {{ $projectContractPayment->activityLogs()->count() }} activity log(s).</b>
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
                                @forelse ($projectContractPayment->modifiedActivityLogs($activitylogLimit) as $perIndex => $perActivityLogDatas)
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
                    @if (($passProjectContactValidation == true) && (Auth::user()->hasUserPermission(["PCPMP04"]) == true))
                        <a href="{{ route("project.contract.payment.edit",["pcSlug" => $projectContract->slug,"slug" => $projectContractPayment->slug]) }}" class="btn btn-primary">Edit</a>
                    @endif

                    @if (($passProjectContactValidation == true) && (Auth::user()->hasUserPermission(["PCPMP05"]) == true))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                            Delete
                        </button>
                    @endif
                </div>
            </div>

            @if (($passProjectContactValidation == true) && (Auth::user()->hasUserPermission(["PCPMP05"]) == true))
                <div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ $projectContractPayment->name }} delete confirmation model</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <ul>
                                        <li>Payment will be deleted.</li>
                                        <li>Payment can not recover.</li>
                                    </ul>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route("project.contract.payment.delete",["pcSlug" => $projectContract->slug,"slug" => $projectContractPayment->slug]) }}" method="POST">
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
                <a role="button" href="{{ route("project.contract.payment.index",["pcSlug" => $projectContract->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to payment
                </a>
            </div>
        </div>
    </div>
@endsection
