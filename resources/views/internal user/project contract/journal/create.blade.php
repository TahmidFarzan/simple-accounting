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
            <li class="breadcrumb-item"><a href="{{ route("project.contract.details",["slug" => $projectContract->slug]) }}">{{ $projectContract->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("project.contract.journal.index",["pcSlug" => $projectContract->slug]) }}">Journal</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')

    @php
        $currentEntryOption = (old("entry_type") == null) ? "Revenue" : old("entry_type");
    @endphp

    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("project.contract.journal.save",["pcSlug" => $projectContract->slug]) }}" method="POST" id="createForm">
                @csrf

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="nameInput" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Entry date <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="entryDateInput" name="entry_date" type="date" class="form-control form-control-sm @error('entry_date') is-invalid @enderror" value="{{ (old('entry_date') == null) ? date('Y-m-d',strtotime(now())) : date('Y-m-d',strtotime(old('entry_date'))) }}" required>
                                    @error('entry_date')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Entry type <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <div class="pt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('entry_type') is-invalid @enderror" type="radio" name="entry_type" id="entryRypeRevenueOption" value="Revenue" @if($currentEntryOption == "Revenue") checked @endif >
                                            <label class="form-check-label" for="entryRypeRevenueOption">Revenue</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('entry_type') is-invalid @enderror" type="radio" name="entry_type" id="entryRypeRevenueLoss" value="Loss" @if($currentEntryOption == "Loss") checked @endif >
                                            <label class="form-check-label" for="entryRypeRevenueLoss">Loss</label>
                                        </div>
                                    </div>
                                    @error('entry_type')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Amount ({{ $setting["businessSetting"]["currency_symbol"] }})  <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="amountInput" name="amount" type="number" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ old('amount') }}" min="0" placeholder="Ex: 0.0" step="0.01" required>
                                    @error('amount')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Description</label>
                                <div class="col-lg-8">
                                    <textarea id="descriptionInput" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Ex: Hello">{{ old('description') }}</textarea>

                                    @error('description')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Note <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <textarea id="noteInput" name="note" class="form-control form-control-sm @error('note') is-invalid @enderror" placeholder="Ex: Hello" required>{{ old('note') }}</textarea>

                                    @error('note')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4 mb-3">
                        <button type="submit" class="btn btn-outline-success">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("project.contract.journal.index",["pcSlug" => $projectContract->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to journal
                </a>
            </div>
        </div>
    </div>
@endsection
