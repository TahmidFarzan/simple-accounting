@extends('layouts.app')

@section('mainPageName')
    Project contract
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item active" aria-current="page">Project contract</li>
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
                    @if (Auth::user()->hasUserPermission(["PCMP02"]) == true)
                        <a href="{{ route("project.contract.create") }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create project contract</a>
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
                            <input type="text" name="selected_nav_tab" id="selectedNavTabForSorting" class="form-control form-control-sm" readonly required value="Ongoing" hidden>
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
                                    <label class="col-md-4 col-form-label col-form-label-sm">Receivable status</label>
                                    <div class="col-md-8">
                                        <select class="form-control form-control-sm" id="receivableStatusInputForSorting" name="receivable_status">
                                            <option value="">Select</option>
                                            <option value="All">All</option>
                                            @foreach ( $receivableStatuses as $perStatus)
                                                <option value="{{ $perStatus }}">{{ $perStatus }}</option>
                                            @endforeach
                                        </select>
                                        <div id="receivableStatusInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-md-4 col-form-label col-form-label-sm">Client</label>
                                    <div class="col-md-8">
                                        <select class="form-control form-control-sm" id="clientInputForSorting" name="client">
                                            <option value="">Select</option>
                                            <option value="All">All</option>
                                            @foreach ( $projectContractClients as $perClient)
                                                <option value="{{ $perClient->slug }}">{{ $perClient->name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="clientInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-md-4 col-form-label col-form-label-sm">Category</label>
                                    <div class="col-md-8">
                                        <select class="form-control form-control-sm" id="categoryInputForSorting" name="category">
                                            <option value="">Select</option>
                                            <option value="All">All</option>
                                            <x-project_contract.project_contract.form.categories :categories="$projectContractCategories" :activeCategorySlug="null"/>
                                        </select>
                                        <div id="categoryInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-md-4 col-form-label col-form-label-sm">Start date</label>
                                    <div class="col-md-8">
                                        <div class="input-group mb-3">
                                            <input type="date" class="form-control form-control-sm" id="startDateInputForSorting" name="start_date">
                                            <select id="startDateConditionInputForSorting" name="start_date_condition" class="form-control form-control-sm">
                                                @foreach (array(""=>"Select","="=>"Equal",">"=>"Less than","<"=>"Greater than",">="=>"Equal or less than","<="=>"Equal or greater than") as $perConditionIndex => $perCondition)
                                                    <option value="{{ $perConditionIndex }}">{{ $perCondition }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="startDateInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                        <div id="startDateConditionInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label class="col-md-4 col-form-label col-form-label-sm">End date</label>
                                    <div class="col-md-8">
                                        <div class="input-group mb-3">
                                            <input type="date" class="form-control form-control-sm" id="endDateInputForSorting" name="end_date">
                                            <select id="endDateConditionInputForSorting" name="end_date_condition" class="form-control form-control-sm">
                                                @foreach (array(""=>"Select","="=>"Equal",">"=>"Less than","<"=>"Greater than",">="=>"Equal or less than","<="=>"Equal or greater than") as $perConditionIndex => $perCondition)
                                                    <option value="{{ $perConditionIndex }}">{{ $perCondition }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="endDateInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
                                        <div id="endDateConditionInputForSortingErrorMessageDiv" class="alert alert-danger mt-2 p-1" style="display: none;"></div>
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
                        <button class="nav-link active" id="ongoingNavTab" data-bs-toggle="tab" data-bs-target="#ongoingNavTabDiv" type="button" role="tab">Ongoing</button>
                        <button class="nav-link" id="completeNavTab" data-bs-toggle="tab" data-bs-target="#completeNavTabDiv" type="button" role="tab">Complete</button>
                    </div>
                </nav>
                <div class="tab-content" id="tabGroupDivContent">
                    <div class="tab-pane fade show active" id="ongoingNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Client</th>
                                            <th>Category</th>
                                            <th>Date range</th>
                                            <th>Receivable ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                            <th>Receivable status</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ongoingProjectContracts as $perProjectContractIndex => $perProjectContract)
                                            <tr>
                                                <td>{{ $perProjectContractIndex + 1 }}</td>
                                                <td>{{ $perProjectContract->name }}</td>
                                                <td>{{ $perProjectContract->client->name }}</td>
                                                <td>{{ $perProjectContract->category->name }}</td>
                                                <td>
                                                    @php
                                                        $dateRange = "<p>";
                                                        $dateRange = $dateRange.'<b>Start date :</b> '.( ($perProjectContract->start_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->start_date)) ).'<br/>';
                                                        $dateRange = $dateRange.'<b>End date :</b> '.( ($perProjectContract->end_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->end_date)) ).'<br/>';
                                                        $dateRange = $dateRange."</p>";
                                                    @endphp

                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="date-range-popover" data-bs-title="Date range information" data-bs-content="{{ $dateRange }}">
                                                        {{ ( ($perProjectContract->start_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->start_date)) ) }} to {{ ( ($perProjectContract->end_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->end_date)) ) }}
                                                    </button>
                                                </td>
                                                <td>
                                                    @php
                                                        $investedPopOver = "<p>";
                                                        $investedPopOver = $investedPopOver.'<b>Invested :</b> '.$perProjectContract->invested_amount." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver.'<b>Total revenue :</b> '.$perProjectContract->totalRevenueAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver.'<b>Total loss :</b> '.$perProjectContract->totalLossAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver.'<b>Total receivable :</b> '.$perProjectContract->totalReceivableAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver."</p>";
                                                    @endphp

                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="invested-amount-popover" data-bs-title="Invented amount information" data-bs-content="{{ $investedPopOver }}">
                                                        {{ $perProjectContract->totalReceivableAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                                    </button>
                                                </td>
                                                <td>
                                                    <span class="badge p-2 @if($perProjectContract->receivable_status == "NotStarted") text-bg-primary @endif @if($perProjectContract->receivable_status == "Due") text-bg-warning @endif @if($perProjectContract->receivable_status == "Partial") text-bg-secondary @endif @if($perProjectContract->receivable_status == "Complete") text-bg-success @endif" style="font-size: 13px;"> {{ ($perProjectContract->receivable_status == "NotStarted") ? "Not started" : $perProjectContract->receivable_status }}</span>
                                                </td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["PCMP03"]) == true)
                                                        <a href="{{ route("project.contract.details",["slug" => $perProjectContract->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCMP04"]) == true)
                                                        <a href="{{ route("project.contract.edit",["slug" => $perProjectContract->slug]) }}" class="btn btn-sm btn-primary m-1">Edit</a>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCMP05"]) == true)
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#ongoing{{str_replace("-","",$perProjectContract->slug) }}DeleteConfirmationModal">
                                                            Delete
                                                        </button>
                                                        <div class="modal fade" id="ongoing{{str_replace("-","",$perProjectContract->slug) }}DeleteConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perProjectContract->name }} delete confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Project contract will be deleted.</li>
                                                                                <li>Can not recover rroject contract.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("project.contract.delete",["slug" => $perProjectContract->slug]) }}" method="POST">
                                                                            @csrf
                                                                            @method("DELETE")
                                                                            <button type="submit" class="btn btn-sm btn-success">Yes, delete it.</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCMP06"]) == true)
                                                        <button type="button" class="btn btn-sm btn-dark m-1" data-bs-toggle="modal" data-bs-target="#ongoing{{str_replace("-","",$perProjectContract->slug) }}CompleteProjectContractConfirmationModal">
                                                            Complete
                                                        </button>
                                                        <div class="modal fade" id="ongoing{{str_replace("-","",$perProjectContract->slug) }}CompleteProjectContractConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perProjectContract->name }} complete project contract confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Status will be complete.</li>
                                                                                <li>Can not return to previous status.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("project.contract.complete.project.contract",["slug" => $perProjectContract->slug]) }}" method="POST">
                                                                            @csrf
                                                                            @method("PATCH")
                                                                            <button type="submit" class="btn btn-sm btn-success">Yes, complete project contract.</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCJMP01"]) == true)
                                                        <a href="{{ route("project.contract.journal.index",["pcSlug" => $perProjectContract->slug]) }}" class="btn btn-sm btn-secondary m-1">Journals</a>
                                                    @endif

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">
                                                    <b class="d-flex justify-content-center text-warning">No project contract found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div id="ongoingNavTabPaginationDiv" class="mb-1">
                                {{ $ongoingProjectContracts->links() }}
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="completeNavTabDiv" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Client</th>
                                            <th>Category</th>
                                            <th>Date range</th>
                                            <th>Receivable ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                            <th>Receivable status</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($completeProjectContracts as $perProjectContractIndex => $perProjectContract)
                                            <tr>
                                                <td>{{ $perProjectContractIndex + 1 }}</td>
                                                <td>{{ $perProjectContract->name }}</td>
                                                <td>{{ $perProjectContract->client->name }}</td>
                                                <td>{{ $perProjectContract->category->name }}</td>
                                                <td>
                                                    @php
                                                        $dateRange = "<p>";
                                                        $dateRange = $dateRange.'<b>Start date :</b> '.( ($perProjectContract->start_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->start_date)) ).'<br/>';
                                                        $dateRange = $dateRange.'<b>End date :</b> '.( ($perProjectContract->end_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->end_date)) ).'<br/>';
                                                        $dateRange = $dateRange."</p>";
                                                    @endphp

                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="date-range-popover" data-bs-title="Date range information" data-bs-content="{{ $dateRange }}">
                                                        {{ ( ($perProjectContract->start_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->start_date)) ) }} - {{ ( ($perProjectContract->end_date == null) ? "Not added." : date('d-M-Y',strtotime($perProjectContract->end_date)) ) }}
                                                    </button>
                                                </td>
                                                <td>
                                                    @php
                                                        $investedPopOver = "<p>";
                                                        $investedPopOver = $investedPopOver.'<b>Invested :</b> '.$perProjectContract->invested_amount." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver.'<b>Total revenue :</b> '.$perProjectContract->totalRevenueAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver.'<b>Total loss :</b> '.$perProjectContract->totalLossAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver.'<b>Total receivable :</b> '.$perProjectContract->totalReceivableAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver.'<b>Total receive :</b> '.$perProjectContract->totalReceiveAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver.'<b>Total due :</b> '.$perProjectContract->totalDueAmount()." ".$setting["businessSetting"]["currency_symbol"].'<br/>';
                                                        $investedPopOver = $investedPopOver."</p>";
                                                    @endphp

                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="invested-amount-popover" data-bs-title="Invented amount information" data-bs-content="{{ $investedPopOver }}">
                                                        {{ $perProjectContract->totalRevenueAmount() }} {{ $setting["businessSetting"]["currency_symbol"] }}
                                                    </button>
                                                </td>
                                                <td>
                                                    <span class="badge p-2 @if($perProjectContract->receivable_status == "NotStarted") text-bg-primary @endif @if($perProjectContract->receivable_status == "Due") text-bg-warning @endif @if($perProjectContract->receivable_status == "Partial") text-bg-secondary @endif @if($perProjectContract->receivable_status == "Complete") text-bg-success @endif" style="font-size: 13px;"> {{ ($perProjectContract->receivable_status == "NotStarted") ? "Not started" : $perProjectContract->receivable_status }}</span>
                                                </td>
                                                <td>
                                                    @if (Auth::user()->hasUserPermission(["PCMP03"]) == true)
                                                        <a href="{{ route("project.contract.details",["slug" => $perProjectContract->slug]) }}" class="btn btn-sm btn-info m-1">Details</a>
                                                    @endif

                                                    @if (($perProjectContract->receivable_status == "NotStarted") && (Auth::user()->hasUserPermission(["PCMP07"]) == true))
                                                        <button type="button" class="btn btn-sm btn-dark m-1" data-bs-toggle="modal" data-bs-target="#complete{{str_replace("-","",$perProjectContract->slug) }}ReceivingPaymentConfirmationModal">
                                                            Start receive payment
                                                        </button>
                                                        <div class="modal fade" id="complete{{str_replace("-","",$perProjectContract->slug) }}ReceivingPaymentConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perProjectContract->name }} receiving payment confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Payment can be added.</li>
                                                                                <li>Receivable status will be due.</li>
                                                                                <li>Can not return to previous receivable status.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("project.contract.start.receiving.payment",["slug" => $perProjectContract->slug]) }}" method="POST">
                                                                            @csrf
                                                                            @method("PATCH")
                                                                            <button type="submit" class="btn btn-sm btn-success">Yes, start payment receiving.</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (!($perProjectContract->receivable_status == "NotStarted") && !($perProjectContract->receivable_status == "Complete") && ($perProjectContract->totalDueAmount() == 0) && (Auth::user()->hasUserPermission(["PCMP08"]) == true))
                                                        <button type="button" class="btn btn-sm btn-dark m-1" data-bs-toggle="modal" data-bs-target="#complete{{str_replace("-","",$perProjectContract->slug)}}CompleteReceivingPaymentConfirmationModal">
                                                            Complete receive payment
                                                        </button>
                                                        <div class="modal fade" id="complete{{str_replace("-","",$perProjectContract->slug) }}CompleteReceivingPaymentConfirmationModal" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5">{{ $perProjectContract->name }} complete receiving payment confirmation model</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <ul>
                                                                                <li>Payment can not be added.</li>
                                                                                <li>Receivable status will be complete.</li>
                                                                                <li>Can not return to previous receivable status.</li>
                                                                            </ul>
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                                                                        <form action="{{ route("project.contract.complete.receiving.payment",["slug" => $perProjectContract->slug]) }}" method="POST">
                                                                            @csrf
                                                                            @method("PATCH")
                                                                            <button type="submit" class="btn btn-sm btn-success">Yes, complete payment receiving.</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["PCJMP01"]) == true)
                                                        <a href="{{ route("project.contract.journal.index",["pcSlug" => $perProjectContract->slug]) }}" class="btn btn-sm btn-secondary m-1">Journals</a>
                                                    @endif

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">
                                                    <b class="d-flex justify-content-center text-warning">No project contract found.</b>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div id="completeNavTabPaginationDiv" class="mb-1">
                                {{ $completeProjectContracts->links() }}
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
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

            $(document).on('click', "#ongoingNavTabPaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#completeNavTabPaginationDiv .pagination .page-item a", function () {
                event.preventDefault();
                var paginationiteUrl = $(this).attr('href');
                var paginationUrlArray = paginationiteUrl.split("?");
                parameterString = parameterGenerate();
                var paginationParameter = (parameterString == null) ? paginationUrlArray[1] : parameterString + "&" + paginationUrlArray[1];
                dataTableLoad(paginationParameter);
            });

            $(document).on('click', "#ongoingNavTab", function () {
                $("#selectedNavTabForSorting").val(null);
                $("#selectedNavTabForSorting").val("Ongoing");
            });

            $(document).on('click', "#completeNavTab", function () {
                $("#selectedNavTabForSorting").val(null);
                $("#selectedNavTabForSorting").val("Complete");
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

            $(document).on('change', "#receivableStatusInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Receivable status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"receivableStatusInputForSorting");
            });

            $(document).on('change', "#clientInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Client status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"clientInputForSorting");
            });

            $(document).on('change', "#categoryInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Category status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"categoryInputForSorting");
            });

            $(document).on('change', "#startDateInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    if($("#startDateConditionInputForSorting").val().length > 0){
                        dataTableLoad(parameterGenerate());
                    }
                }
                else{
                    errorMessages.push("Start date status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"startDateInputForSorting");
            });

            $(document).on('change', "#startDateConditionInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("Start date condition status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"startDateConditionInputForSorting");
            });

            $(document).on('change', "#endDateInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    if($("#endDateConditionInputForSorting").val().length > 0){
                        dataTableLoad(parameterGenerate());
                    }
                }
                else{
                    errorMessages.push("End date status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"endDateInputForSorting");
            });

            $(document).on('change', "#endDateConditionInputForSorting", function () {
                var errorMessages = [];
                if($(this).val().length > 0){
                    dataTableLoad(parameterGenerate());
                }
                else{
                    errorMessages.push("End date condition status is empty.");
                }
                hideOrShowInputFieldErrorMessages(errorMessages,"endDateConditionInputForSorting");
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
            $.each( [
                    "paginationInputForSorting",
                    "receivableStatusInputForSorting",
                    "clientInputForSorting",
                    "categoryInputForSorting",
                    "startDateInputForSorting",
                    "startDateConditionInputForSorting",
                    "endDateInputForSorting",
                    "endDateConditionInputForSorting",
                    "searchInputForSorting",
                    "selectedNavTabForSorting"
                ], function( key, perInput ) {
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
                url: "{{ route('project.contract.index') }}" + "?" + parameterString,
                success: function(result) {
                    $("#extraErrorMessageDiv").hide();
                    $("#extraErrorMessageDiv").html("");;

                    switch ($("#selectedNavTabForSorting").val()) {
                        case "Ongoing":
                            $("#ongoingNavTabDiv").html($(result).find("#ongoingNavTabDiv").html());
                        break;

                        default:
                            $("#completeNavTabDiv").html($(result).find("#completeNavTabDiv").html());
                        break;
                    }
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


@push('onPageExtraCss')
    <style>
        .invested-amount-popover {
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
