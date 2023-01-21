@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump inventory
@endsection

@section('mainCardTitle')
    Create
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $oilAndGasPump->slug]) }}">{{ $oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.inventory.index",["oagpSlug" => $oilAndGasPump->slug]) }}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>
    </nav>
@endsection


@section('authContentOne')
    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("oil.and.gas.pump.inventory.save",["oagpSlug" => $oilAndGasPump->slug]) }}" method="POST" id="createForm">
                @csrf

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-5 col-form-label col-form-label-sm text-bold">Product <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-7">
                                    <select id="productInput" name="product" class="form-control form-control-sm @error('product') is-invalid @enderror" required>
                                        <option value="">Select</option>
                                        @foreach ($products as $perProduct)
                                            <option value="{{ $perProduct->slug }}">{{ $perProduct->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('product')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2"></div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-5 col-form-label col-form-label-sm text-bold">Quantity <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-7">
                                    <input id="quantityInput" name="quantity" type="number" class="form-control form-control-sm @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" placeholder="Ex: Hello" min="0" step="0.01" required>
                                    @error('quantity')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-5 col-form-label col-form-label-sm text-bold">Sell price <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-7">
                                    <input id="sellPriceInput" name="sell_price" type="number" class="form-control form-control-sm @error('sell_price') is-invalid @enderror" value="{{ old('sell_price') }}" placeholder="Ex: Hello" min="0" step="0.01" required>
                                    @error('sell_price')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-5 col-form-label col-form-label-sm text-bold">Purchase price <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-7">
                                    <input id="purchasePriceInput" name="purchase_price" type="number" class="form-control form-control-sm @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price') }}" placeholder="Ex: Hello" min="0" step="0.01" required>
                                    @error('purchase_price')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-5 col-form-label col-form-label-sm text-bold">Previous quantity <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-7">
                                    <input id="previousQuantityInput" name="previous_quantity" type="number" class="form-control form-control-sm @error('previous_quantity') is-invalid @enderror" value="{{ old('previous_quantity') }}" placeholder="Ex: Hello" min="0" step="0.01" required>
                                    @error('previous_quantity')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-5 col-form-label col-form-label-sm text-bold">Previous sell price <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-7">
                                    <input id="previousSellPriceInput" name="previous_sell_price" type="number" class="form-control form-control-sm @error('previous_sell_price') is-invalid @enderror" value="{{ old('previous_sell_price') }}" placeholder="Ex: Hello" min="0" step="0.01" required>
                                    @error('previous_sell_price')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-5 col-form-label col-form-label-sm text-bold">Previous purchase price <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-7">
                                    <input id="previousPurchasePriceInput" name="previous_purchase_price" type="number" class="form-control form-control-sm @error('previous_purchase_price') is-invalid @enderror" value="{{ old('previous_purchase_price') }}" placeholder="Ex: Hello" min="0" step="0.01" required>
                                    @error('previous_purchase_price')
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
                <a role="button" href="{{ route("oil.and.gas.pump.inventory.index",["oagpSlug" => $oilAndGasPump->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to oil and gas pump inventory
                </a>
            </div>
        </div>
    </div>
@endsection
