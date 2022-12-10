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
            <h5 class="card-title">Business quick view</h5>
        </div>
        <div class="card-body text-dark">
            <nav>
                <div class="nav nav-tabs" id="businessQuickViewNavTabList" role="tablist">
                    <button class="nav-link active" id="businessQuickViewProjectContractNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractNavContent" aria-selected="true">Project contract</button>
                    <button class="nav-link" id="patnershipBusinessNavTab" data-bs-toggle="tab" data-bs-target="#patnershipBusinessNavContent" type="button" role="tab" aria-controls="patnershipBusinessNavContent" aria-selected="false">Patnership Business</button>
                </div>
            </nav>
            <div class="tab-content" id="businessQuickViewNavTabContent">
                <div class="tab-pane fade show active" id="businessQuickViewProjectContractNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractNavTab" tabindex="0">
                    <div class="card-body">
                        <nav>
                            <div class="nav nav-tabs" id="businessQuickViewProjectContractNavGroupTab" role="tablist">
                                <button class="nav-link active" id="businessQuickViewProjectContractCurrentNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractCurrentNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractCurrentNavContent" aria-selected="true">Current</button>
                                <button class="nav-link" id="businessQuickViewProjectContractPreviousNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractPreviousNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractPreviousNavContent" aria-selected="false">Previous</button>
                            </div>
                        </nav>

                        <div class="tab-content" id="businessQuickViewProjectContractNavGroupTabContent">
                            <div class="tab-pane fade show active" id="businessQuickViewProjectContractCurrentNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractCurrentNavTab" tabindex="0">
                                <div class="card-body">
                                    <nav>
                                        <div class="nav nav-tabs" id="businessQuickViewProjectContractCurrentGroupTab" role="tablist">
                                            <button class="nav-link active" id="businessQuickViewProjectContractCurrentDateNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractCurrentDateNavContant" type="button" role="tab" aria-controls="businessQuickViewProjectContractCurrentDateNavContant" aria-selected="true">Day</button>
                                            <button class="nav-link" id="businessQuickViewProjectContractCurrentWeekNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractCurrentWeekNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractCurrentWeekNavContent" aria-selected="false">Week</button>
                                            <button class="nav-link" id="businessQuickViewProjectContractCurrentMonthNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractCurrentMonthNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractCurrentMonthNavContent" aria-selected="false">Month</button>
                                            <button class="nav-link" id="businessQuickViewProjectContractCurrentYearNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractCurrentYearNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractCurrentYearNavContent" aria-selected="false">Year</button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="businessQuickViewProjectContractCurrentGroupTabContent">
                                        <div class="tab-pane fade show active" id="businessQuickViewProjectContractCurrentDateNavContant" role="tabpanel" aria-labelledby="businessQuickViewProjectContractCurrentDateNavTab" tabindex="0">
                                            <div class="card-body">

                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="businessQuickViewProjectContractCurrentWeekNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractCurrentWeekNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="businessQuickViewProjectContractCurrentMonthNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractCurrentMonthNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="businessQuickViewProjectContractCurrentYearNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractCurrentYearNavTab" tabindex="0">...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="businessQuickViewProjectContractPreviousNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractPreviousNavTab" tabindex="0">
                                <div class="card-body">
                                    <nav>
                                        <div class="nav nav-tabs" id="businessQuickViewProjectContractPreviousGroupTab" role="tablist">
                                            <button class="nav-link active" id="businessQuickViewProjectContractPreviousDateNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractPreviousDateNavContant" type="button" role="tab" aria-controls="businessQuickViewProjectContractPreviousDateNavContant" aria-selected="true">Day</button>
                                            <button class="nav-link" id="businessQuickViewProjectContractPreviousWeekNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractPreviousWeekNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractPreviousWeekNavContent" aria-selected="false">Week</button>
                                            <button class="nav-link" id="businessQuickViewProjectContractPreviousMonthNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractPreviousMonthNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractPreviousMonthNavContent" aria-selected="false">Month</button>
                                            <button class="nav-link" id="businessQuickViewProjectContractPreviousYearNavTab" data-bs-toggle="tab" data-bs-target="#businessQuickViewProjectContractPreviousYearNavContent" type="button" role="tab" aria-controls="businessQuickViewProjectContractPreviousYearNavContent" aria-selected="false">Year</button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="businessQuickViewProjectContractPreviousGroupTabContent">
                                        <div class="tab-pane fade show active" id="businessQuickViewProjectContractPreviousDateNavContant" role="tabpanel" aria-labelledby="businessQuickViewProjectContractPreviousDateNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="businessQuickViewProjectContractPreviousWeekNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractPreviousWeekNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="businessQuickViewProjectContractPreviousMonthNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractPreviousMonthNavTab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="businessQuickViewProjectContractPreviousYearNavContent" role="tabpanel" aria-labelledby="businessQuickViewProjectContractPreviousYearNavTab" tabindex="0">...</div>
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
