@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump product
@endsection

@section('mainCardTitle')
    Create
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $oilAndGasPump->slug]) }}">{{ $oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.product.index",["oagpSlug" => $oilAndGasPump->slug]) }}">Product</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')

    @php
        $typeOption = old("type");

        if($typeOption == null){
            $typeOption = "Oil";
        }

        $addToInventory = old("add_to_inventory");

        if($addToInventory == null){
            $addToInventory = "No";
        }
    @endphp

    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("oil.and.gas.pump.product.save",["oagpSlug" => $oilAndGasPump->slug]) }}" method="POST" id="createForm">
                @csrf

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="nameInput" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
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
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Type <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('type') is-invalid @enderror" type="radio" name="type" id="typeOilOption" value="Oil" @if ($typeOption == "Oil") checked @endif>
                                            <label class="form-check-label" for="typeOilOption">Oil</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('type') is-invalid @enderror" type="radio" name="type" id="gasOilOption" value="Gas" @if ($typeOption == "Gas") checked @endif>
                                            <label class="form-check-label" for="gasOilOption">Gas</label>
                                        </div>
                                    </div>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Add to inventory <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('add_to_inventory') is-invalid @enderror" type="radio" name="add_to_inventory" id="addToInventoryNoOption" value="No" @if ($addToInventory == "No") checked @endif>
                                            <label class="form-check-label" for="addToInventoryNoOption">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('add_to_inventory') is-invalid @enderror" type="radio" name="add_to_inventory" id="addToInventoryYesOption" value="Yes" @if ($addToInventory == "Yes") checked @endif>
                                            <label class="form-check-label" for="addToInventoryYesOption">Yes</label>
                                        </div>
                                    </div>
                                    @error('add_to_inventory')
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
                <a role="button" href="{{ route("oil.and.gas.pump.product.index",["oagpSlug" => $oilAndGasPump->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to oil and gas pump product
                </a>
            </div>
        </div>
    </div>
@endsection
