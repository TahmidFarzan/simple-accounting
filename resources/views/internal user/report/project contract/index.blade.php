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
            <li class="breadcrumb-item active" aria-current="page">Project contract</li>
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
                        <label class="col-md-4 col-form-label col-form-label-sm">Status</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="statusInputForGenerateReport" name="status">
                                <option value="">Select</option>
                                <option value="All">All</option>
                                @foreach ( $statuses as $perStatus)
                                    <option value="{{ $perStatus }}">{{ $perStatus }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Receivable status</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="receivableStatusInputForGenerateReport" name="receivable_status">
                                <option value="">Select</option>
                                <option value="All">All</option>
                                @foreach ( $receivableStatuses as $perStatus)
                                    <option value="{{ $perStatus }}">{{ $perStatus }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Category</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="categoryInputForGenerateReport" name="category">
                                <option value="">Select</option>
                                <option value="All">All</option>
                                <x-report.project_contract.form.categories :categories="$projectContractCategories" :activeCategorySlug="null"/>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Start date</label>
                        <div class="col-md-8">
                            <input type="date" class="form-control form-control-sm" id="startDateInputForGenerateReport" name="start_date">
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">End date</label>
                        <div class="col-md-8">
                            <input type="date" class="form-control form-control-sm" id="endDateInputForGenerateReport" name="end_date">
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <div class="row">
                        <label class="col-md-4 col-form-label col-form-label-sm">Client</label>
                        <div class="col-md-8">
                            <select class="form-control form-control-sm" id="clientInputForGenerateReport" name="client">
                                <option value="">Select</option>
                                <option value="All">All</option>
                                @foreach ( $projectContractClients as $perClient)
                                    <option value="{{ $perClient->slug }}">{{ $perClient->name }}</option>
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
            @if ($projectContracts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Category</th>
                                <th>Start date</th>
                                <th>End date</th>
                                <th>Receivable ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                <th>Receivable status</th>
                                <th>Status</th>
                                <th>Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projectContracts as $perProjectContractIndex => $perProjectContract)
                                <tr>
                                    <td>{{ $perProjectContractIndex + 1 }}</td>
                                    <td>{{ $perProjectContract->name }}</td>
                                    <td>{{ $perProjectContract->client->name }}</td>
                                    <td>{{ $perProjectContract->category->name }}</td>
                                    <td>{{ ( ($perProjectContract->start_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->start_date)) ) }}</td>
                                    <td>{{ ( ($perProjectContract->end_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->end_date)) ) }}</td>
                                    <td>
                                        @php
                                            $receivablePopOver = "<p>";
                                            $receivablePopOver = $receivablePopOver.'<b>Invested :</b> '.$perProjectContract->invested_amount." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                            $receivablePopOver = $receivablePopOver.'<b>Total revenue :</b> '.$perProjectContract->totalRevenueAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                            $receivablePopOver = $receivablePopOver.'<b>Total loss :</b> '.$perProjectContract->totalLossAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                            $receivablePopOver = $receivablePopOver.'<b>Total receivable :</b> '.$perProjectContract->totalReceivableAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                            if($perProjectContract->status == "Complete"){
                                                $receivablePopOver = $receivablePopOver.'<b>Total receive :</b> '.$perProjectContract->totalReceiveAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                $receivablePopOver = $receivablePopOver.'<b>Total due :</b> '.$perProjectContract->totalDueAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                            }

                                            $receivablePopOver = $receivablePopOver."</p>";
                                        @endphp

                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="receivable-amount-popover" data-bs-title="Receivable amount information" data-bs-content="{{ $receivablePopOver }}">
                                            {{ $perProjectContract->totalReceivableAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge p-2 @if($perProjectContract->receivable_status == "NotStarted") text-bg-primary @endif @if($perProjectContract->receivable_status == "Due") text-bg-warning @endif @if($perProjectContract->receivable_status == "Partial") text-bg-secondary @endif @if($perProjectContract->receivable_status == "Complete") text-bg-success @endif" style="font-size: 13px;"> {{ ($perProjectContract->receivable_status == "NotStarted") ? "Not started" : $perProjectContract->receivable_status }}</span>
                                    </td>
                                    <td>
                                        <span class="badge p-2 @if($perProjectContract->status == "Ongoing") text-bg-primary @endif @if($perProjectContract->status == "Complete") text-bg-success @endif" style="font-size: 13px;"> {{  $perProjectContract->status }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route("report.project.contract.details",["slug" => $perProjectContract->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <b class="d-flex justify-content-center text-warning">No project contract found.</b>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="paginationDiv" class="mb-1">
                    {{ $projectContracts->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection

@push("onPageExtraScript")
    <script>
        $(document).ready(function(){
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        });
    </script>
@endpush


@push('onPageExtraCss')
    <style>
        .receivable-amount-popover {
            --bs-popover-max-width: auto;
            --bs-popover-border-color: var(--bs-primary);
            --bs-popover-header-bg: var(--bs-primary);
            --bs-popover-header-color: var(--bs-white);
            --bs-popover-body-padding-x: 1rem;
            --bs-popover-body-padding-y: .5rem;
        }

        .date-range-popover {
            --bs-popover-max-width: auto;
            --bs-popover-border-color: var(--bs-primary);
            --bs-popover-header-bg: var(--bs-primary);
            --bs-popover-header-color: var(--bs-white);
            --bs-popover-body-padding-x: 1rem;
            --bs-popover-body-padding-y: .5rem;
        }
    </style>
@endpush
