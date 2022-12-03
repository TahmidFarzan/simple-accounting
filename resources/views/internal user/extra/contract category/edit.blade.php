@extends('layouts.app')

@section('mainPageName')
    Contract category
@endsection

@section('mainCardTitle')
    Edit
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("contract.category.index") }}">Contract category</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')

    @php
        $hasAParentOptionCheckedStatus = "Yes";
        $activeCategorySlug = (old("parent") == null) ? (($contractCategory->parent_id == null) ? null : $contractCategory->parentCategory->slug ) : old("parent") ;
        if($contractCategory->parent_id == null){
            if(old("has_a_parent") == null){
                $hasAParentOptionCheckedStatus = "No";
            }
            else{
                if((old("has_a_parent") == "Yes")){
                    $hasAParentOptionCheckedStatus = "Yes";
                }
                else{
                    $hasAParentOptionCheckedStatus = "No";
                }
            }
        }
        else{
            if(old("has_a_parent") == null){
                $hasAParentOptionCheckedStatus = "Yes";
            }
            else{
                if((old("has_a_parent") == "Yes")){
                    $hasAParentOptionCheckedStatus = "Yes";
                }
                else{
                    $hasAParentOptionCheckedStatus = "No";
                }
            }
        }
    @endphp

    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("contract.category.update",["slug" => $contractCategory->slug]) }}" method="POST" id="editForm">
                @csrf
                @method("PATCH")
                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-md-5 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8 col-md-7">
                                    <input id="name" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ (old('name') == null) ? $contractCategory->name : old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-lg-4 col-md-5 col-form-label col-form-label-sm text-bold">Code <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8 col-md-7">
                                    <input id="code" name="code" type="text" class="form-control form-control-sm @error('code') is-invalid @enderror" value="{{ (old('code') == null) ? $contractCategory->code : old('code') }}" placeholder="Ex: Hello" maxlength="200" required>
                                    @error('code')
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
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Description</label>
                                <div class="col-lg-8 col-md-7">
                                    <textarea id="descriptionInput" name="description" type="text" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Ex: Hello">{{ (old('description') == null) ? $contractCategory->description : old('description') }}</textarea>

                                    @error('description')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6"></div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <label class="col-lg-4 col-md-5 col-form-label col-form-label-sm text-bold">Has a parent <i class="fa-solid fa-asterisk mt-2" style="font-size: 10px;!important"></i></label>
                        <div class="col-lg-8 col-md-7 mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="hasAParentInputYesOption" name="has_a_parent" value="Yes" {{ ($hasAParentOptionCheckedStatus == "Yes") ? "checked" : null }}>
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="hasAParentInputNoOption" name="has_a_parent" value="No" {{ ($hasAParentOptionCheckedStatus == "No") ? "checked" : null }}>
                                <label class="form-check-label">No</label>
                            </div>
                            @error('has_a_parent')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3" id="parentInputDiv" @if ($hasAParentOptionCheckedStatus == "No") style="display: none" @endif>
                    <div class="row">
                        <label class="col-lg-4 col-md-5 col-form-label text-bold">Parent <i class="fa-solid fa-asterisk mt-2" style="font-size: 10px;!important"></i></label>
                        <div class="col-lg-8 col-md-7">
                            <select id="parentInput" name="parent" class="form-control form-control-sm @error('parent') is-invalid @enderror" @if ($hasAParentOptionCheckedStatus == "No") hidden disabled @endif @if ($hasAParentOptionCheckedStatus == "Yes") required @endif>
                                <option value="">Select</option>
                                <x-contract_category.form.contract-categories :categories="$contractCategories" :activeCategorySlug="$activeCategorySlug"/>
                            </select>

                            @error('parent')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
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
                <a role="button" href="{{ route("contract.category.index") }}" class="btn btn-sm btn-secondary">
                    Go to contract category
                </a>
            </div>
        </div>
    </div>
@endsection

@push("onPageExtraScript")
    <script>
        $(document).ready(function(){
            $(document).on('change', "#editForm input[type=radio][name=has_a_parent]", function () {
                if (this.value == 'Yes') {
                    $("#parentInputDiv").show();

                    $("#parentInput").prop("hidden",false);
                    $("#parentInput").prop("disabled",false);
                    $("#parentInput").prop("readonly",false);
                    $("#parentInput").prop("required",true);
                }

                if (this.value == 'No') {
                    $("#parentInputDiv").hide();

                    $("#parentInput").prop("hidden",true);
                    $("#parentInput").prop("disabled",true);
                    $("#parentInput").prop("readonly",true);
                    $("#parentInput").prop("required",false);
                }
            });
        });
    </script>
@endpush
