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
        $projectContractPopOver = "<p>";
        $projectContractPopOver = $projectContractPopOver."<b>Ongoing :</b> ".$projectContractQuickView["ongoing"]."</br>";
        $projectContractPopOver = $projectContractPopOver."<b>Complete :</b> ".$projectContractQuickView["complete"]."</br>";
        $projectContractPopOver = $projectContractPopOver."<b>Total :</b> ".($projectContractQuickView["ongoing"] + $projectContractQuickView["complete"])."</br>";
        $projectContractPopOver = $projectContractPopOver."</p>";

        $projectContractJournalPopOver = "<p>";
        $projectContractJournalPopOver = $projectContractJournalPopOver."<b>Revenue :</b> ".$projectContractJournalQuickView["revenue"]." ".$setting["businessSetting"]["currency_symbol"]."</br>";
        $projectContractJournalPopOver = $projectContractJournalPopOver."<b>Loss :</b> ".$projectContractJournalQuickView["loss"]." ".$setting["businessSetting"]["currency_symbol"]."</br>";
        $projectContractJournalPopOver = $projectContractJournalPopOver."<b>Balance :</b> ".($projectContractJournalQuickView["revenue"] - $projectContractJournalQuickView["loss"])." ".$setting["businessSetting"]["currency_symbol"]."</br>";
        $projectContractJournalPopOver = $projectContractJournalPopOver."</p>";

    @endphp

    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <div class="d-flex justify-content-center">
                <h5 class="card-title">Project contract quick view</h5>
            </div>

            <div class="row">
                <div class="card-body">
                    <button type="button" class="btn btn-sm btn-primary col-md-4 m-1" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="project-contract-popover" data-bs-title="Receivable amount information" data-bs-content="{{ $projectContractPopOver }}">
                        <b>Total project contract: </b> {{ $projectContractQuickView["ongoing"] + $projectContractQuickView["complete"] }}
                    </button>

                    <button type="button" class="btn btn-sm btn-primary col-md-4 m-1" data-bs-container="body" data-bs-animation="true" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus"  data-bs-placement="top" data-bs-custom-class="project-contract-popover" data-bs-title="Receivable amount information" data-bs-content="{{ $projectContractJournalPopOver }}">
                        <b>Total journal balance :</b>  {{ $projectContractJournalQuickView["revenue"] - $projectContractJournalQuickView["loss"] }} {{ $setting["businessSetting"]["currency_symbol"] }}
                    </button>

                    <button type="button" class="btn btn-sm btn-primary col-md-4 m-1" >
                        <b>Total payment :</b>  {{ $projectContractPaymentQuickView["payment"] }} {{ $setting["businessSetting"]["currency_symbol"] }}
                    </button>
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
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        });
    </script>
@endpush


@push('onPageExtraCss')
    <style>
        .project-contract-popover {
            --bs-popover-max-width: auto;
            --bs-popover-border-color: var(--bs-primary);
            --bs-popover-header-bg: var(--bs-primary);
            --bs-popover-header-color: var(--bs-white);
            --bs-popover-body-padding-x: 1rem;
            --bs-popover-body-padding-y: .5rem;
        }

        .project-contract-journal-popover {
            --bs-popover-max-width: auto;
            --bs-popover-border-color: var(--bs-primary);
            --bs-popover-header-bg: var(--bs-primary);
            --bs-popover-header-color: var(--bs-white);
            --bs-popover-body-padding-x: 1rem;
            --bs-popover-body-padding-y: .5rem;
        }

        .project-contract-journal-payment {
            --bs-popover-max-width: auto;
            --bs-popover-border-color: var(--bs-primary);
            --bs-popover-header-bg: var(--bs-primary);
            --bs-popover-header-color: var(--bs-white);
            --bs-popover-body-padding-x: 1rem;
            --bs-popover-body-padding-y: .5rem;
        }
    </style>
@endpush
