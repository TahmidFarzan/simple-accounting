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
            <li class="breadcrumb-item"><a href="{{ route("report.project.contract.payment.index") }}">Project contract payment</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')

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
                            <th>Payment date</th>
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
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("report.project.contract.payment.index",["slug" => $projectContractPayment->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to payment
                </a>
            </div>
        </div>
    </div>
@endsection
