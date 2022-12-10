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
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')

    @php
        $currentDate = now();
        $currentDateFormat = now()->format('Y-m-d');
        $currentWeekStartDate = $currentDate->startOfWeek()->format('Y-m-d');
        $currentWeekEndDate = $currentDate->endOfWeek()->format('Y-m-d');
        $currentMonthStartDate = $currentDate->startOfMonth()->format('Y-m-d');
        $currentMonthEndDate = $currentDate->endOfMonth()->format('Y-m-d');
        $currentYearStartDate = $currentDate->startOfYear()->format('Y-m-d');
        $currentYearEndDate = $currentDate->endOfYear()->format('Y-m-d');

        $previousDate = now()->subDays(1);
        $previousDateFormat = now()->subDays(1)->format('Y-m-d');
        $previousWeekStartDate = $previousDate->startOfWeek()->format('Y-m-d');
        $previousWeekEndDate = $previousDate->endOfWeek()->format('Y-m-d');
        $previousMonthStartDate = $previousDate->startOfMonth()->format('Y-m-d');
        $previousMonthEndDate = $previousDate->endOfMonth()->format('Y-m-d');
        $previousYearStartDate = $previousDate->startOfYear()->format('Y-m-d');
        $previousYearEndDate = $previousDate->endOfYear()->format('Y-m-d');

    @endphp
    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <h5 class="card-title">Project contract quick view</h5>
        </div>
        <div class="card-body text-dark">
            <nav>
                <div class="nav nav-tabs" id="projectContractQuickViewNavTabList" role="tablist">
                    <button class="nav-link active" id="projectContractQuickViewNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewNavContent" type="button" role="tab" aria-controls="projectContractQuickViewNavContent" aria-selected="true">Project contract</button>
                    <button class="nav-link" id="patnershipBusinessNavTab" data-bs-toggle="tab" data-bs-target="#patnershipBusinessNavContent" type="button" role="tab" aria-controls="patnershipBusinessNavContent" aria-selected="false">Patnership Business</button>
                </div>
            </nav>
            <div class="tab-content" id="projectContractQuickViewNavTabContent">
                <div class="tab-pane fade show active" id="projectContractQuickViewNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewNavTab" tabindex="0">
                    <div class="card-body">
                        <nav>
                            <div class="nav nav-tabs" id="projectContractQuickViewNavGroupTab" role="tablist">
                                <button class="nav-link active" id="projectContractQuickViewCurrentNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewCurrentNavContent" type="button" role="tab" aria-controls="projectContractQuickViewCurrentNavContent" aria-selected="true">Current</button>
                                <button class="nav-link" id="projectContractQuickViewPreviousNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewPreviousNavContent" type="button" role="tab" aria-controls="projectContractQuickViewPreviousNavContent" aria-selected="false">Previous</button>
                            </div>
                        </nav>

                        <div class="tab-content" id="projectContractQuickViewNavGroupTabContent">
                            <div class="tab-pane fade show active" id="projectContractQuickViewCurrentNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewCurrentNavTab" tabindex="0">
                                <div class="card-body">
                                    <nav>
                                        <div class="nav nav-tabs" id="projectContractQuickViewCurrentGroupTab" role="tablist">
                                            <button class="nav-link active" id="projectContractQuickViewCurrentDateNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewCurrentDateNavContant" type="button" role="tab" aria-controls="projectContractQuickViewCurrentDateNavContant" aria-selected="true">Day</button>
                                            <button class="nav-link" id="projectContractQuickViewCurrentWeekNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewCurrentWeekNavContent" type="button" role="tab" aria-controls="projectContractQuickViewCurrentWeekNavContent" aria-selected="false">Week</button>
                                            <button class="nav-link" id="projectContractQuickViewCurrentMonthNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewCurrentMonthNavContent" type="button" role="tab" aria-controls="projectContractQuickViewCurrentMonthNavContent" aria-selected="false">Month</button>
                                            <button class="nav-link" id="projectContractQuickViewCurrentYearNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewCurrentYearNavContent" type="button" role="tab" aria-controls="projectContractQuickViewCurrentYearNavContent" aria-selected="false">Year</button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="projectContractQuickViewCurrentGroupTabContent">
                                        <div class="tab-pane fade show active" id="projectContractQuickViewCurrentDateNavContant" role="tabpanel" aria-labelledby="projectContractQuickViewCurrentDateNavTab" tabindex="0">
                                            <div class="card-body">

                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="projectContractQuickViewCurrentWeekNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewCurrentWeekNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="projectContractQuickViewCurrentMonthNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewCurrentMonthNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="projectContractQuickViewCurrentYearNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewCurrentYearNavTab" tabindex="0">...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="projectContractQuickViewPreviousNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewPreviousNavTab" tabindex="0">
                                <div class="card-body">
                                    <nav>
                                        <div class="nav nav-tabs" id="projectContractQuickViewPreviousGroupTab" role="tablist">
                                            <button class="nav-link active" id="projectContractQuickViewPreviousDateNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewPreviousDateNavContant" type="button" role="tab" aria-controls="projectContractQuickViewPreviousDateNavContant" aria-selected="true">Day</button>
                                            <button class="nav-link" id="projectContractQuickViewPreviousWeekNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewPreviousWeekNavContent" type="button" role="tab" aria-controls="projectContractQuickViewPreviousWeekNavContent" aria-selected="false">Week</button>
                                            <button class="nav-link" id="projectContractQuickViewPreviousMonthNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewPreviousMonthNavContent" type="button" role="tab" aria-controls="projectContractQuickViewPreviousMonthNavContent" aria-selected="false">Month</button>
                                            <button class="nav-link" id="projectContractQuickViewPreviousYearNavTab" data-bs-toggle="tab" data-bs-target="#projectContractQuickViewPreviousYearNavContent" type="button" role="tab" aria-controls="projectContractQuickViewPreviousYearNavContent" aria-selected="false">Year</button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="projectContractQuickViewPreviousGroupTabContent">
                                        <div class="tab-pane fade show active" id="projectContractQuickViewPreviousDateNavContant" role="tabpanel" aria-labelledby="projectContractQuickViewPreviousDateNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="projectContractQuickViewPreviousWeekNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewPreviousWeekNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="projectContractQuickViewPreviousMonthNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewPreviousMonthNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="projectContractQuickViewPreviousYearNavContent" role="tabpanel" aria-labelledby="projectContractQuickViewPreviousYearNavTab" tabindex="0">...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="patnershipBusinessNavContent" role="tabpanel" aria-labelledby="patnershipBusinessNavTab" tabindex="0">...</div>
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
