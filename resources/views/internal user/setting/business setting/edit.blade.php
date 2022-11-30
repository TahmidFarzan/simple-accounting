@extends('layouts.app')

@section('mainPageName')
    Setting
@endsection

@section('mainCardTitle')
    Business information edit
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("setting.index") }}">Setting</a></li>
            <li class="breadcrumb-item"><a href="{{ route("setting.business.setting.index") }}">Business setting</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <form method="POST" action="{{ route('setting.business.setting.update',["slug" => $businessSetting->slug]) }}" enctype="multipart/form-data">
                @csrf

                @method("PATCH")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
                            <div class="row">
                                <label for="nameInput" class="col-lg-4 col-md-5 col-form-label text-bold">Name <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8 col-md-7">
                                    <input id="nameInput" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ (old('name') == null) ? $businessSetting->fields_with_values["name"] : old('name') }}" autocomplete="name" placeholder="Ex: Demo Company" autofocus required maxlength="255">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="row">
                                <label for="shortNameInput" class="col-lg-4 col-md-5 col-form-label text-bold">Short name <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8 col-md-7">
                                    <input id="shortNameInput" type="text" class="form-control @error('short_name') is-invalid @enderror" name="short_name" value="{{ (old('short_name') == null) ? $businessSetting->fields_with_values["short_name"] : old('short_name') }}" required autocomplete="short_name" autofocus placeholder="Ex: DC" maxlength="15">
                                    @error('short_name')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
                            <div class="row">
                                <label for="emailInput" class="col-lg-4 col-md-5 col-form-label text-bold">Email <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8 col-md-7">
                                    <input id="emailInput" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ (old('email') == null) ? $businessSetting->fields_with_values["email"] : old('email') }}" autocomplete="email" autofocus placeholder="Ex: xxx@gmail.com" maxlength="255">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="row">
                                <label for="mobileNoInput" class="col-lg-4 col-md-5 col-form-label text-bold">Mobile no <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8 col-md-7">
                                    <input id="mobileNoInput" type="tel" class="form-control @error('mobile_no') is-invalid @enderror calling-code-country" name="mobile_no" value="{{ (old('mobile_no') == null) ? $businessSetting->fields_with_values["mobile_no"] : old('mobile_no') }}" autocomplete="mobile_no" placeholder="Ex: +8801671786285" maxlength="20">
                                    <span id="mobileNoValidationError" class="invalid-feedback" role="alert" style="display: none;"></span>
                                    @error('mobile_no')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="row">
                                <label for="urlInput" class="col-lg-4 col-md-5 col-form-label text-bold">Url</label>
                                <div class="col-lg-8 col-md-7">
                                    <input id="urlInput" type="url" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ (old('url') == null) ? $businessSetting->fields_with_values["url"] : old('url') }}" autocomplete="url" autofocus placeholder="Ex: https://www.google.com/">
                                    @error('url')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <label for="addressInput" class="col-lg-2 col-md-3 col-form-label text-bold">Address</label>
                        <div class="col-lg-10 col-md-9">
                            <textarea id="addressInput" class="form-control @error('address') is-invalid @enderror" name="address" autocomplete="address" placeholder="Ex: Kaligonj,Jhenaidah,BD">{{ (old('address') == null) ? $businessSetting->fields_with_values["address"] : old('address') }}</textarea>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <label for="descriptionInput" class="col-lg-2 col-md-3 col-form-label text-bold">Description</label>
                        <div class="col-lg-10 col-md-9">
                            <textarea id="descriptionInput" class="form-control @error('description') is-invalid @enderror" name="description" autocomplete="description" placeholder="Ex: Sell clothes.">{{ (old('description') == null) ? $businessSetting->fields_with_values["description"] : old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="row">
                                <label for="countryInput" class="col-lg-4 col-md-5 col-form-label text-bold">Country <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8 col-md-7">
                                    <input id="countryInput" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ (old('country') == null) ? $businessSetting->fields_with_values["country"] : old('country') }}" placeholder="Ex: Demo Company" required maxlength="100">
                                    @error('country')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label for="countryCodeInput" class="col-lg-4 col-form-label text-bold">Country code <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="countryCodeInput" type="text" class="form-control @error('country_code') is-invalid @enderror" name="country_code" value="{{ (old('country_code') == null) ? $businessSetting->fields_with_values["country_code"] : old('country_code') }}" required placeholder="Ex: DC" maxlength="3">
                                    @error('country_code')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="row">
                                <label for="currencyInput" class="col-md-4 col-form-label text-bold">Currency <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="currencyInput" type="text" class="form-control @error('currency') is-invalid @enderror" name="currency" value="{{ (old('currency') == null) ? $businessSetting->fields_with_values["currency"] : old('currency') }}" placeholder="Ex: Demo Company" required maxlength="50">
                                    @error('currency')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label for="currencyCodeInput" class="col-md-4 col-form-label text-bold">Currency code <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="currencyCodeInput" type="text" class="form-control @error('currency_code') is-invalid @enderror" name="currency_code" value="{{ (old('currency_code') == null) ? $businessSetting->fields_with_values["currency_code"] : old('currency_code') }}" required placeholder="Ex: DC" maxlength="3">
                                    @error('currency_code')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label for="currencySymbolInput" class="col-md-4 col-form-label text-bold">Currency symbol <i class="fa-solid fa-asterisk float-end" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="currencySymbolInput" type="text" class="form-control @error('currency_symbol') is-invalid @enderror" name="currency_symbol" value="{{ (old('currency_symbol') == null) ? $businessSetting->fields_with_values["currency_symbol"] : old('currency_symbol') }}" required placeholder="Ex: D" maxlength="2">
                                    @error('currency_symbol')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
                            <div class="row">
                                <label for="logoImageInput" class="col-lg-4 col-md-5 col-form-label text-bold">Logo</label>
                                <div class="col-lg-8 col-md-7">
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logoImageInput" name="logo"  value="{{ (old('logo') == null) ? $businessSetting->fields_with_values["logo"] : old('logo') }}" accept="image/png,image/jpg,image/jpeg,image/webp">

                                    @error('logo')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div id="logoImagePreviewDiv" @if (($businessSetting->fields_with_values["logo"] == null)) style="display: none;" @endif>
                                <img id="logoImagePreview" src="{{ (old('logo') == null) ? ( ($businessSetting->fields_with_values["logo"] == null) ? asset("images/setting/default-logo.png") : asset("storage/images/setting/".$businessSetting->fields_with_values["logo"]) ) : asset("storage/images/setting/business-setting/".old('logo')) }}" class="img-thumbnail" alt="Business logo">
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
                <a role="button" href="{{ route("setting.business.setting.index") }}" class="btn btn-sm btn-secondary">
                    Go to business setting
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
                initialCountry:"",
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

            $('#logoImageInput').change(function(){
                const file = this.files[0];
                if (file){
                    $('#logoImagePreviewDiv').show();
                    let reader = new FileReader();
                    reader.onload = function(event){
                        $('#logoImagePreview').attr('src', event.target.result);
                    }
                    reader.readAsDataURL(file);
                }
                else{
                    $('#logoImagePreview').attr('src', "");
                    $('#logoImagePreviewDiv').hide();
                }
            });
        });
    </script>
@endpush

@push('onPageExtraCss')
    <link href="{{ asset("intlTelInput/intlTelInput.min.css") }}" rel="stylesheet">
@endpush

