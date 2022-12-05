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
            <li class="breadcrumb-item">Project contract</li>
            <li class="breadcrumb-item"><a href="{{ route("project.contract.client.index") }}">Client</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    @php
        $currentGender = (old('gender') == null) ? $projectContractClient->gender : old('gender');
    @endphp

    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("project.contract.client.update",["slug" => $projectContractClient->slug]) }}" method="POST" id="editForm">
                @csrf

                @method("PATCH")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input type="text" id="nameInput" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ (old('name') == null) ? $projectContractClient->name :  old('name')}}" placeholder="Ex: Hello" maxlength="200" required>
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
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Gender <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <select class="form-control form-control-sm @error('gender') is-invalid @enderror" id="genderInput" name="gender" required>
                                        <option value="">Please select gender</option>
                                        @foreach (array("Male","Female","Other") as $perGender)
                                            <option value="{{ $perGender }}" {{ ($currentGender == $perGender) ? 'selected' : null }}>{{ $perGender }}</option>
                                        @endforeach
                                    </select>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Mobile no</label>
                                <div class="col-lg-8">
                                    <input type="text" id="mobileNoInput" name="mobile_no" class="form-control form-control-sm @error('mobile_no') is-invalid @enderror" value="{{ (old('mobile_no') == null) ? $projectContractClient->mobile_no :  old('mobile_no')}}" placeholder="Ex: 16XXXXXXX" maxlength="20">
                                    <div class="invalid-feedback" id="mobileNoInputExtraError" style="display: none;"></div>
                                    @error('mobile_no')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Email</label>
                                <div class="col-lg-8">
                                    <input type="email" id="emailInput" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ (old('email') == null) ? $projectContractClient->email :  old('email')}}" placeholder="Ex: hello@xx.com" maxlength="255">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Address</label>
                                <div class="col-lg-8">
                                    <textarea id="addressInput" name="address" class="form-control form-control-sm @error('address') is-invalid @enderror" placeholder="Ex:Kaligonj,Jhenaidah">{{ (old('address') == null) ? $projectContractClient->address :  old('address')}}</textarea>
                                    @error('address')
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
                                    <textarea id="descriptionInput" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Ex: Hello">{{ (old('description') == null) ? $projectContractClient->description : old('description') }}</textarea>

                                    @error('description')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Note</label>
                                <div class="col-lg-8">
                                    <textarea id="noteInput" name="note" class="form-control form-control-sm @error('note') is-invalid @enderror" placeholder="Ex: Hello">{{ (old('note') == null) ? $projectContractClient->note : old('note') }}</textarea>

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
                <a role="button" href="{{ route("project.contract.client.index") }}" class="btn btn-sm btn-secondary">
                    Go to client
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
            var errorMap = [ "invalid number", "invalid country code", "too short", "too long", "Invalid number"];
            var mobileNoInput = document.querySelector("#mobileNoInput");

            var mobileNoInputPlugin=window.intlTelInput(mobileNoInput,({
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
                        $('#mobileNoInputExtraError').html(null);
                        $('#mobileNoInputExtraError').css("display","none");
                        if(formErrorCount>0){
                            formErrorCount=formErrorCount-1;
                        }
                    }
                    else{
                        formErrorCount=formErrorCount+1;
                        $('#mobileNoInputExtraError').css("display","block");
                        $('#mobileNoInputExtraError').html('<strong>'+"Mobile no is "+errorMap[mobileNoInputPlugin.getValidationError()]+'</strong>');
                    }
                }
            });

            $("#editForm").submit(function(e){
                if(formErrorCount > 0){
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
