@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump product
@endsection

@section('mainCardTitle')
    Edit
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $product->oilAndGasPump->slug]) }}">{{ $product->oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.product.index",["oagpSlug" => $product->oilAndGasPump->slug]) }}">Product</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')

    @php
        $typeOption = old("type");

        if($typeOption == null){
            $typeOption = "Oil";
        }
    @endphp

    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("oil.and.gas.pump.product.update",["oagpSlug" => $product->oilAndGasPump->slug,"pSlug" => $product->slug]) }}" method="POST" id="updateForm">
                @csrf

                @method("PATCH")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="nameInput" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ (old('name') == null) ? $product->name : old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
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
                    Go to product
                </a>
            </div>
        </div>
    </div>
@endsection
