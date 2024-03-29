@extends('layouts.app')

@section('mainPageName')
    User
@endsection

@section('mainCardTitle')
    Create
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("user.index") }}">User</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    @php
        $userPermissionGroupIsRequired = false;
        $allSelectedUserPermissionGroups = array();
        $useDefaultPassword = (old("default_password") == null) ? "Yes" : old("default_password");
        $useAutoEmailVerify = (old("auto_email_verify") == null) ? "No" : old("auto_email_verify");

        if(old('user_role') == "Subordinate"){
            $userPermissionGroupIsRequired = true;
        }
        else{
            $userPermissionGroupIsRequired = false;
        }

        if(!(old('user_permission_group') == null)){
            $allSelectedUserPermissionGroups = old("user_permission_group");
        }
    @endphp
    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("user.save") }}" method="POST" id="createForm">
                @csrf

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input type="text" id="nameInput" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
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
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">User role <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <select class="form-control form-control-sm @error('user_role') is-invalid @enderror" id="userRoleInput" name="user_role" required>
                                        <option value="">Select</option>
                                        @foreach ($userRoles as $perUserRole)
                                            <option value="{{ $perUserRole }}" {{ (old('user_role') == $perUserRole) ? 'selected' : null }}>{{ $perUserRole }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_role')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Email <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input type="email" id="emailInput" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Ex: hello@xx.com" maxlength="255" required>
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
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Mobile no </label>
                                <div class="col-lg-8">
                                    <input type="text" id="mobileNoInput" name="mobile_no" class="form-control form-control-sm @error('mobile_no') is-invalid @enderror" value="{{ old('mobile_no') }}" placeholder="Ex: 16XXXXXXX" maxlength="20">
                                    <div class="invalid-feedback" id="mobileNoInputIntlTelInputExtraError" style="display: none;"></div>
                                    @error('mobile_no')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2" id="userPermissionGroupDiv" @if($userPermissionGroupIsRequired == false) hidden @endif>
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">User permission group <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <select class="form-control form-control-sm @error('user_permission_group') is-invalid @enderror" id="userPermissionGroupInput" name="user_permission_group[]" multiple  @if($userPermissionGroupIsRequired == false) disabled hidden @endif @if($userPermissionGroupIsRequired == true) required @endif>
                                        @foreach ($userPermissionGroups as $perUserPermissionGroup)
                                            <option value="{{ $perUserPermissionGroup->slug }}" {{ (in_array($perUserPermissionGroup->slug,$allSelectedUserPermissionGroups) == true) ? 'selected' : null }}>{{ $perUserPermissionGroup->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_permission_group')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Auto email verify <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <div class=" pt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="auto_email_verify" id="autoEmailVerifyOptionYes" value="Yes" {{ ($useAutoEmailVerify == "Yes") ? "checked" : null }}>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="auto_email_verify" id="autoEmailVerifyOptionNo" value="No" {{ ($useAutoEmailVerify == "No") ? "checked" : null }}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>

                                    @error('auto_email_verify')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Default password <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <div class="pt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="default_password" id="defaultPasswordOptionYes" value="Yes" {{ ($useDefaultPassword == "Yes") ? "checked" : null }}>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="default_password" id="defaultPasswordOptionNo" value="No" {{ ($useDefaultPassword == "No") ? "checked" : null }}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <input type="password" id="passwordInput" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror" value="{{ old('password') }}" placeholder="Enter your password." maxlength="255"  @if($useDefaultPassword == "No") required @endif @if($useDefaultPassword == "Yes") disabled readonly hidden @endif>
                                    </div>

                                    @error('default_password')
                                        <span class="invalid-feedback mb-1" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    @error('password')
                                        <span class="invalid-feedback mb-1" role="alert" style="display: block;">
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
                <a role="button" href="{{ route("user.index") }}" class="btn btn-sm btn-secondary">
                    Go to user
                </a>
            </div>
        </div>
    </div>
@endsection

@push('onPageExtraScript')
    <script src="{{ asset("intlTelInput/intlTelInput.min.js") }}"></script>
    <script src="{{ asset("intlTelInput/intlTelInput-jquery.min.js") }}"></script>
    <script src="{{ asset("bootstrap-multiselect/bootstrap-multiselect.min.js") }}"></script>
    <script>
        $(document).ready(function(){
            var formErrorCount=0;
            var errorMap = [ "invalid number", "invalid country code", "too short", "too long", "Invalid number"];
            var mobileNoInput = document.querySelector("#mobileNoInput");

            var mobileNoInputPlugin = window.intlTelInput(mobileNoInput,({
                allowDropdown: true,
                autoPlaceholder: "aggressive",
                autoHideDialCode: true,
                customPlaceholder: null,
                dropdownContainer: null,
                excludeCountries: [],
                formatOnDisplay: true,
                hiddenInput: "",
                initialCountry: "",
                localizedCountries: "",
                nationalMode: false,
                placeholderNumberType: "MOBILE",
                preferredCountries: ["BD"],
                separateDialCode: false,
                utilsScript: "{{ asset('intlTelInput/utils.js') }}",
            }));

            mobileNoInput.addEventListener('blur', function() {
                if(mobileNoInput.value.trim()){
                    if(mobileNoInputPlugin.isValidNumber()){
                        $('#mobileNoInputIntlTelInputExtraError').html(null);
                        $('#mobileNoInputIntlTelInputExtraError').css("display","none");
                        if(formErrorCount>0){
                            formErrorCount=formErrorCount-1;
                        }
                    }
                    else{
                        formErrorCount=formErrorCount+1;
                        $('#mobileNoInputIntlTelInputExtraError').css("display","block");
                        $('#mobileNoInputIntlTelInputExtraError').html('<strong>'+"Mobile no is "+errorMap[mobileNoInputPlugin.getValidationError()]+'</strong>');
                    }
                }
            });

            $('#userPermissionGroupInput').multiselect({
                buttonWidth: '100%',
                enableFiltering: true,
                includeResetOption: true,
                includeResetDivider: true,
                filterPlaceholder: 'Search',
                includeFilterClearBtn: true,
                includeSelectAllOption: true,
                includeSelectAllOption: true,
                enableClickableOptGroups: true,
                enableCollapsibleOptGroups: true,

                templates: {
                    divider: '<div class="dropdown-divider"></div>',
                    popupContainer: '<div class="multiselect-container dropdown-menu"></div>',
                    resetButton: '<div class="multiselect-reset text-center p-2"><button type="button" class="btn btn-sm btn-block btn-outline-secondary"></button></div>',
                    filter: '<div class="multiselect-filter d-flex align-items-center"><input type="search" class="multiselect-search form-control form-control-sm self" style="margin-left:0rem !important;"/></div>',
                    button: '<button type="button" id="userPermissionGroupMultiSelectDropDownButtton" class="multiselect dropdown-toggle form-control form-control-sm" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span></button>',
                },
            });

            $("#createForm").submit(function(e){
                if(formErrorCount > 0){
                    e.preventDefault();
                }
                else{
                    return true;
                }
            });

            $(document).on('change','input[type=radio][name=default_password]', function () {
                if($(this).val() == "Yes"){
                    $("#passwordInput").prop("hidden",true);
                    $("#passwordInput").prop("readonly",true);
                    $("#passwordInput").prop("disabled",true);
                    $("#passwordInput").prop("required",false);
                }
                else{
                    $("#passwordInput").prop("hidden",false);
                    $("#passwordInput").prop("readonly",false);
                    $("#passwordInput").prop("disabled",false);
                    $("#passwordInput").prop("required",true);
                }
            });

            $(document).on('change','#userRoleInput', function () {
                if($(this).val() == "Subordinate"){
                    $("#userPermissionGroupDiv").prop("hidden",false);
                    $("#userPermissionGroupInput").prop("hidden",false);
                    $("#userPermissionGroupInput").prop("disabled",false);
                    $("#userPermissionGroupInput").prop("required",true);
                    $("#userPermissionGroupMultiSelectDropDownButtton").prop("disabled",false);

                    $("#userPermissionGroupMultiSelectDropDownButtton").removeClass("disabled");
                }
                else{
                    $("#userPermissionGroupDiv").prop("hidden",true);
                    $("#userPermissionGroupInput").prop("hidden",true);
                    $("#userPermissionGroupInput").prop("disabled",true);
                    $("#userPermissionGroupInput").prop("required",false);
                    $("#userPermissionGroupMultiSelectDropDownButtton").prop("disabled",true);

                    $("#userPermissionGroupMultiSelectDropDownButtton").addClass("disabled");
                }
            });
        });
    </script>
@endpush

@push('onPageExtraCss')
    <link href="{{ asset("intlTelInput/intlTelInput.min.css") }}" rel="stylesheet">
    <link href="{{ asset("bootstrap-multiselect/bootstrap-multiselect.min.css") }}" rel="stylesheet">
@endpush
