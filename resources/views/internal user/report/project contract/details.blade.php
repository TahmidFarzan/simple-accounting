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
            <li class="breadcrumb-item">Report</li>
            <li class="breadcrumb-item"><a href="{{ route("report.project.contract.index") }}">Project contract</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
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
                                                <span class="badge p-2 @if($projectContract->status == "Ongoing") text-bg-primary @endif @if($projectContract->status == "Complete") text-bg-success @endif" style="font-size: 13px;"> {{ $projectContract->status }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Receivable status</th>
                                            <th>:</th>
                                            <td>
                                                <span class="badge p-2 @if($projectContract->receivable_status == "NotStarted") text-bg-primary @endif @if($projectContract->receivable_status == "Due") text-bg-warning @endif @if($projectContract->receivable_status == "Partial") text-bg-secondary @endif @if($projectContract->receivable_status == "Complete") text-bg-success @endif" style="font-size: 13px;"> {{ ($projectContract->receivable_status == "NotStarted") ? "Not started" : $projectContract->receivable_status }}</span>
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
                                            <td>{{ $projectContract->invested_amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total revenue</th>
                                            <th>:</th>
                                            <td>
                                                {{ $projectContract->totalRevenue() }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Total loss</th>
                                            <th>:</th>
                                            <td>
                                                {{ $projectContract->totalLoss() }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Receivable</th>
                                            <th>:</th>
                                            <td>
                                                {{ $projectContract->totalReceivable() }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Income</th>
                                            <th>:</th>
                                            <td>
                                                {{ $projectContract->totalIncome() }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                            </td>
                                        </tr>

                                        @if ($projectContract->status == "Complete")
                                            <tr>
                                                <th>Receive</th>
                                                <th>:</th>
                                                <td>
                                                    {{ $projectContract->totalReceive() }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>Due</th>
                                                <th>:</th>
                                                <td>
                                                    {{ $projectContract->totalDue() }} {{ $setting["businessSetting"]["currency_symbol"] }}</span>
                                                </td>
                                            </tr>
                                        @endif
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

        <div class="card-body text-dark mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-secondary">
                        <div class="card-body text-dark">
                            <b class="d-flex justify-content-center mb-1">
                                Journals
                            </b>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Note</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @forelse ($projectContract->journals as $perJournalIndex  => $perJournal)
                                                <tr>
                                                    <td>{{ $perJournalIndex +1 }}</td>
                                                    <td>{{ $perJournal->name }}</td>
                                                    <td>{{date('d-M-Y',strtotime($perJournal->entry_date)) }} </td>
                                                    <td><span class="badge p-2 @if($perJournal->entry_type == "Loss") text-bg-warning @endif @if($perJournal->entry_type == "Revenue") text-bg-success @endif" style="font-size: 13px;"> {{ $perJournal->entry_type }}</span></td>
                                                    <td>{{($perJournal->description == null) ? "Not added yet." : $perJournal->description }}</td>
                                                    <td>
                                                        <ul>
                                                            @forelse ($perJournal->note as $perNote)
                                                                <li>{{ $perNote }}</li>
                                                            @empty
                                                                <li><b class="d-flex justify-content-center text-warning">Not added.</b></li>
                                                            @endforelse
                                                        </ul>
                                                    </td>
                                                    <td>{{ $perJournal->amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7">
                                                        <b class="d-flex justify-content-center text-warning">No journal found.</b>
                                                    </td>
                                                </tr>
                                            @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6">Balance</th>
                                            <td>{{ $projectContract->journals()->where("entry_type","Revenue")->sum("amount") -  $projectContract->journals()->where("entry_type","Loss")->sum("amount")}} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (($projectContract->status == "Complete") && !($projectContract->receivable_status == "NotStarted"))
            <div class="card-body text-dark mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-secondary">
                            <div class="card-body text-dark">
                                <b class="d-flex justify-content-center mb-1">
                                    Payments
                                </b>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Method</th>
                                                <th>Description</th>
                                                <th>Note</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                @forelse ($projectContract->payments as $perPaymentIndex  => $perPayment)
                                                    <tr>
                                                        <td>{{ $perPaymentIndex +1 }}</td>
                                                        <td>{{ $perPayment->name }}</td>
                                                        <td>{{date('d-M-Y',strtotime($perPayment->entry_date)) }} </td>
                                                        <td>{{ $perPayment->paymentMethod->name }}</td>
                                                        <td>{{($perPayment->description == null) ? "Not added yet." : $perPayment->description }}</td>
                                                        <td>
                                                            <ul>
                                                                @forelse ($perPayment->note as $perNote)
                                                                    <li>{{ $perNote }}</li>
                                                                @empty
                                                                    <li><b class="d-flex justify-content-center text-warning">Not added.</b></li>
                                                                @endforelse
                                                            </ul>
                                                        </td>
                                                        <td>{{ $perPayment->amount }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7">
                                                            <b class="d-flex justify-content-center text-warning">No payment found.</b>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="6">Balance</th>
                                                <td>{{ $projectContract->payments->sum("amount") }} {{ $setting["businessSetting"]["currency_symbol"] }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("report.project.contract.index") }}" class="btn btn-sm btn-secondary">
                    Go to project contract
                </a>
            </div>
        </div>
    </div>
@endsection

