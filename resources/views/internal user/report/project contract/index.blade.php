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
        <div class="card-body text-dark">

        </div>

        <div class="card-body text-dark">

        </div>
    </div>
@endsection

