@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump supplier
@endsection

@section('mainCardTitle')
    Edit
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $supplier->oilAndGasPump->slug]) }}">{{ $supplier->oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.supplier.index",["oagpSlug" => $supplier->oilAndGasPump->slug]) }}">Supplier</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("oil.and.gas.pump.supplier.update",["oagpSlug" => $supplier->oilAndGasPump->slug,"sSlug" => $supplier->slug]) }}" method="POST" id="updateForm">
                @csrf

                @method("PATCH")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="nameInput" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ (old('name') == null) ? $supplier->name :  old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
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
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Email</label>
                                <div class="col-md-8">
                                    <input id="emailInput" name="email" type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ (old('email') == null) ? $supplier->email : old('email') }}" placeholder="Ex: xx@xx.com" maxlength="255">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Mobile no</label>
                                <div class="col-md-8">
                                    <input id="mobileNoInput" name="mobile_no" type="text" class="form-control form-control-sm @error('mobile_no') is-invalid @enderror" value="{{ (old('mobile_no') == null) ? $supplier->mobliel_no : old('mobile_no')}}" placeholder="Ex: +880161XXXXXXX" maxlength="20">
                                    <span id="mobileNoValidationError" class="invalid-feedback" role="alert" style="display: none;"></span>
                                    @error('mobile_no')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Description</label>
                                <div class="col-lg-8">
                                    <textarea id="descriptionInput" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Ex: Hello">{{ (old('description') == null) ? $supplier->description : old('description') }}</textarea>

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
                <a role="button" href="{{ route("oil.and.gas.pump.index") }}" class="btn btn-sm btn-secondary">
                    Go to supplier
                </a>
            </div>
        </div>
    </div>
@endsection

@push('onPageExtraScript')
    <script src="{{ asset("intlTelInput/intlTelInput.min.js") }}"></script>
    <script src="{{ asset("intlTelInput/intlTelInput-jquery.min.js") }}"></script>
    <script>
        $(document).ready(function(){
            var formErrorCount=0;
            var errorMap = [ "Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
            var mobileNoInput = document.querySelector("#mobileNoInput");
            var mobileNoInputPlugin = window.intlTelInput(mobileNoInput,({
                allowDropdown:true,
                autoPlaceholder:"aggressive",
                autoHideDialCode:true,
                customPlaceholder:null,
                dropdownContainer:null,
                excludeCountries: [],
                formatOnDisplay:true,
                geoIpLookup:function(success, failure) {
                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        var countryCode = (resp && resp.country) ? resp.country : "";
                        success(countryCode);
                    });
                },
                hiddenInput:"",
                initialCountry:"BD",
                localizedCountries:"",
                nationalMode:false,
                placeholderNumberType:"MOBILE",
                preferredCountries: [],
                separateDialCode:false,
                utilsScript:"{{ asset('intlTelInput/utils.js') }}",
            }));

            mobileNoInput.addEventListener('blur', function() {
                if(mobileNoInput.value.trim()){
                    if(mobileNoInputPlugin.isValidNumber()){
                        $('#mobileNoValidationError').html(null);
                        $('#mobileNoValidationError').css("display","none");
                        if(formErrorCount>0){
                            formErrorCount=formErrorCount-1;
                        }
                    }
                    else{
                        formErrorCount=formErrorCount+1;
                        $('#mobileNoValidationError').css("display","block");
                        $('#mobileNoValidationError').html('<strong>'+errorMap[mobileNoInputPlugin.getValidationError()]+'</strong>');
                    }
                }
            });

            $("form").submit(function(e){
                if(formErrorCount>0){
                    e.preventDefault();
                }
                else{
                    return true;
                }
            });

        });
    </script>
@endpush

@push('onPageExtraCss')
    <link href="{{ asset("intlTelInput/intlTelInput.min.css") }}" rel="stylesheet">
@endpush
