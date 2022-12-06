@extends('layouts.app')

@section('mainPageName')
    Project contract
@endsection

@section('mainCardTitle')
    Edit
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("project.contract.index") }}">Project contract</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    @php
        $disabledStatus = array();
        $disabledReceivableStatus = array();

        $currentDate = (old("start_date") == null) ? (($projectContract->start_date == null) ? now() : $projectContract->start_date) : old("start_date");

        $currentDateToTime = strtotime($currentDate);
        $currentStatus = (old("status") == null) ? $projectContract->status : old("status");
        $currentClient = (old("client") == null) ? $projectContract->client->slug : old("client");
        $currentCategory = (old("category") == null) ? $projectContract->category->slug : old("category");
        $currentEndToTime = (old("end_date") == null) ? ( ($projectContract->end_date == null) ? null : strtotime($projectContract->end_date)) : strtotime(old("end_date"));
        $currentStartDateToTime = (old("start_date") == null) ? ((strtotime($projectContract->start_date) == null) ? null : strtotime($projectContract->start_date) ) : strtotime(old("start_date"));
        $currentReceivableStatus = (old("receivable_status") == null) ? $projectContract->receivable_status : old("receivable_status");

        if(!($currentStartDateToTime == null)){
            if($currentStartDateToTime <= $currentDateToTime){
                if(($currentEndToTime == $currentDateToTime) || ($currentEndToTime > $currentDateToTime)){
                    if($currentEndToTime == $currentDateToTime){
                        $disabledStatus = array("Upcoming");
                    }
                    else{
                        $disabledStatus = array("Comelete","Upcoming");
                    }
                }
                else{
                    $disabledStatus = array("Ongoing","Upcoming");
                }
            }
            else{
                $disabledStatus = array("Comelete","Ongoing");
            }
        }

        if(!($currentStatus == null)){
            if(in_array($currentStatus,array("Ongoing","Upcoming"))){
                $disabledReceivableStatus = array("Due","Partial","Full");
            }
            else{
                $disabledReceivableStatus = array();
            }
        }
    @endphp

    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("project.contract.update",["slug" => $projectContract->slug]) }}" method="POST" id="createForm">
                @csrf
                @method("patch")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="nameInput" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ (old('name') == null) ? $projectContract->name : old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
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
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Code <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="codeInput" name="code" type="text" class="form-control form-control-sm @error('code') is-invalid @enderror" value="{{ (old('code') == null) ? $projectContract->code : old('code') }}" placeholder="Ex: Hello" maxlength="200" required>
                                    @error('code')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Start date </label>
                                <div class="col-md-8">
                                    <input id="startDateInput" name="start_date" type="date" class="form-control form-control-sm @error('start_date') is-invalid @enderror" value="{{ (old('start_date') == null) ? $projectContract->start_date : old('start_date') }}" >
                                    @error('start_date')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">End date </label>
                                <div class="col-md-8">
                                    <input id="endDateInput" name="end_date" type="date" class="form-control form-control-sm @error('end_date') is-invalid @enderror" value="{{ (old('end_date') == null) ? $projectContract->end_date : old('end_date') }}" >
                                    @error('end_date')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Status <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                        <div class="col-md-8">
                                            <select class="form-control form-control-sm @error('status') is-invalid @enderror" id="statusInput" name="status">
                                                <option value="">Select</option>
                                                @foreach ( $statuses as $perStatus)
                                                    <option value="{{ $perStatus }}" {{ ($currentStatus == $perStatus ) ? "selected" : null }} {{ (in_array(Str::studly($perStatus),$disabledStatus)) ? "disabled" : null }}>{{ $perStatus}}</option>
                                                @endforeach
                                            </select>

                                            @error('status')
                                                <span class="invalid-feedback" role="alert" style="display: block;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Receivable status <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                        <div class="col-md-8">
                                            <select class="form-control form-control-sm @error('receivable_status') is-invalid @enderror" id="receivableStatusInput" name="receivable_status">
                                                <option value="">Select</option>
                                                @foreach ( $receivableStatuses as $perStatus)
                                                    <option value="{{ Str::studly($perStatus) }}" {{ ($currentReceivableStatus == Str::studly($perStatus) ) ? "selected" : null }}  {{ (in_array(Str::studly($perStatus),$disabledReceivableStatus)) ? "disabled" : null }}>{{ $perStatus}}</option>
                                                @endforeach
                                            </select>

                                            @error('receivable_status')
                                                <span class="invalid-feedback" role="alert" style="display: block;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Category <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <select class="form-control form-control-sm @error('category') is-invalid @enderror" id="categoryInput" name="category">
                                        <option value="">Select</option>
                                        <x-project_contract.project_contract.form.categories :categories="$categories" :activeCategorySlug="$currentCategory"/>
                                    </select>

                                    @error('category')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Client <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <select class="form-control form-control-sm @error('client') is-invalid @enderror" id="clientInput" name="client">
                                        <option value="">Select</option>
                                        @foreach ( $clients as $perClient)
                                            <option value="{{ $perClient->slug }}" {{ ($currentClient == $perClient->slug ) ? "selected" : null }}>{{ $perClient->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('client')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Invested amount ({{ $setting["businessSetting"]["currency_symbol"] }}) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="investedAmountInput" name="invested_amount" type="number" class="form-control form-control-sm @error('invested_amount') is-invalid @enderror" value="{{ (old('invested_amount') == null) ? $projectContract->invested_amount : old('invested_amount') }}" placeholder="Ex: 0" min="0" step="0.01">
                                    @error('invested_amount')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Description</label>
                                        <div class="col-md-8">
                                            <textarea id="descriptionInput" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Ex: Hello">{{ (old('description') == null) ? $projectContract->description : old('description') }}</textarea>

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
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Note <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                        <div class="col-md-8">
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
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4 mb-3">
                        <button type="submit" class="btn btn-outline-success">
                            Update
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
                <a role="button" href="{{ route("project.contract.index") }}" class="btn btn-sm btn-secondary">
                    Go to project contract
                </a>
            </div>
        </div>
    </div>
@endsection

@push('onPageExtraScript')
    <script src="{{ asset("jquery/jquery-dateformat.min.js") }}"></script>
    <script>
        $(document).ready(function(){
            $(document).on('change', "#startDateInput", function () {
                statusUpdateAccrodingDate();
            });

            $(document).on('change', "#endDateInput", function () {
                statusUpdateAccrodingDate();
            });

            $(document).on('change', "#statusInput", function () {
                receiveStatusUpdateAccrodingStatus();
            });
        });

        function statusUpdateAccrodingDate(){
            var selectedEndDate = $("#endDateInput").val();
            var selectedStartDate = $("#startDateInput").val();
            var currentDate = $.format.date(new Date(), "yyyy-MM-dd");

            $("#statusInput option:disabled").prop('disabled',false);

            if(selectedStartDate.length > 0){
                if(new Date(selectedStartDate) <= new Date(currentDate))
                {
                    $("#statusInput").val(null);
                    $("#statusInput option[value='Upcoming']").prop('disabled',true);
                    $("#statusInput option[value='Upcoming']").siblings().prop('disabled',false);

                    if(selectedEndDate.length > 0){
                        if((new Date(selectedEndDate) > new Date(currentDate)) || (new Date(selectedEndDate) == new Date(currentDate))){
                            $("#statusInput").val("Ongoing");
                            $("#statusInput option:selected").siblings().prop('disabled',true);
                        }
                        if(new Date(selectedEndDate) < new Date(currentDate)){
                            $("#statusInput").val("Complete");
                            $("#statusInput option:selected").siblings().prop('disabled',true);
                        }
                    }
                }

                if(new Date(selectedStartDate) > new Date(currentDate)){
                    $("#statusInput").val("Upcoming");
                    $("#statusInput option:selected").siblings().prop('disabled',true);
                }
            }
            receiveStatusUpdateAccrodingStatus();
        }

        function receiveStatusUpdateAccrodingStatus(){
            var statusInput = $("#statusInput").val();
            if(statusInput.length > 0){
                    if(statusInput == "Complete"){
                        $("#receivableStatusInput").val(null);
                        $("#receivableStatusInput option:disabled").prop('disabled',false);
                    }
                    else{
                        $("#receivableStatusInput").val("NotStarted");
                        $("#receivableStatusInput option:selected").siblings().prop('disabled',true);
                    }
                }
                else{
                    $("#receivableStatusInput").val(null);
                    $("#receivableStatusInput option:disabled").prop('disabled',false);
                }
        }
    </script>
@endpush
