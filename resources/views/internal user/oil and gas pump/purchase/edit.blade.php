@extends('layouts.app')

@section('mainPageName')
    Oil and gas pump purchase
@endsection

@section('mainCardTitle')
    Index
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.index") }}">Oil and gas pump</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $oilAndGasPumpPurchase->oilAndGasPump->slug]) }}">{{ $oilAndGasPumpPurchase->oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.purchase.index",["oagpSlug" => $oilAndGasPumpPurchase->oilAndGasPump->slug]) }}">Purchase</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')

    @php
        $invoiceDate = (old('date') == null) ? $oilAndGasPumpPurchase->date : now();
        $dataTableRow = array('min' => $oilAndGasPumpPurchase->purchaseItems->count(),'max' => 100);
        $supplier = (old('supplier') == null) ? $oilAndGasPumpPurchase->supplier->slug : old('supplier');
        $purchaseItemRow = (old("table_row") == null) ? $oilAndGasPumpPurchase->purchaseItems->count() : old("table_row");
    @endphp


    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("oil.and.gas.pump.purchase.update",["oagpSlug" =>  $oilAndGasPumpPurchase->oilAndGasPump->slug,"puSlug" => $oilAndGasPumpPurchase->slug]) }}" method="POST" id="createForm">
                @csrf
                @method("PATCH")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Date <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="dateInput" name="date" type="date" class="form-control form-control-sm @error('date') is-invalid @enderror" value="{{ date('Y-m-d', strtotime($invoiceDate)) }}" max="{{ date('Y-m-d', strtotime($invoiceDate)) }}" required>
                                    @error('date')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Invoice <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="invoiceInput" name="invoice" type="text" class="form-control form-control-sm @error('invoice') is-invalid @enderror" value="{{ (old('invoice') == null) ? $oilAndGasPumpPurchase->invoice : old('invoice') }}" placeholder="Ex: 15663" maxlength="200" required>
                                    @error('invoice')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Supplier <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <select id="supplierInput" name="supplier" class="form-control form-control-sm @error('supplier') is-invalid @enderror" required>
                                        <option value="">Select</option>
                                        @foreach ($suppliers as $perSupplier)
                                            <option value="{{ $perSupplier->slug }}" {{ ( $supplier == $perSupplier->slug) ? "selected" : null }}>{{ $perSupplier->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('supplier')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="nameInput" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ (old('name') == null) ? $oilAndGasPumpPurchase->name : old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2" hidden>
                                    <input id="tableRow" name="table_row" type="number" class="form-control form-control-sm" value="{{ (old("table_row") == null) ? $dataTableRow["min"] : old("table_row") }}" min="{{ $dataTableRow["min"] }}" max="{{ $dataTableRow["max"] }}" required readonly hidden>
                                </div>

                                <div class="col-md-6">
                                    <button id="addRow" type="button" class="btn btn-sm btn-success"><i class="fa-solid fa-plus"></i></button>
                                    <button id="removeRow" type="button" class="btn btn-sm btn-danger" hidden><i class="fa-solid fa-minus"></i></button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Product *</th>
                                            <th>Quantity *</th>
                                            <th>Sell price ({{ $setting["businessSetting"]["currency_symbol"] }}) *</th>
                                            <th>Purchase price ({{ $setting["businessSetting"]["currency_symbol"] }}) *</th>
                                            <th>Discount (%) *</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($purchaseItemRow == null)
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <select id="productInput1" name="product[]" class="form-control form-select-sm " required>
                                                        <option value="">Select</option>
                                                        @foreach ($products as $perOilAndGasPumpProduct)
                                                            <option value="{{ $perOilAndGasPumpProduct->slug }}">{{ $perOilAndGasPumpProduct->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="quantityInput1" name="quantity[]" type="number" class="form-control form-control-sm" value="0" min="0" step="1" required>
                                                </td>
                                                <td>
                                                    <input id="sellPriceInput1" name="sell_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required>
                                                </td>
                                                <td>
                                                    <input id="purchasePriceInput1" name="purchase_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required>
                                                </td>
                                                <td>
                                                    <input id="discountInput1" name="purchase_discount[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required>
                                                </td>
                                                <td hidden>
                                                    <input id="rowTotalInput1" name="total_purchase_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required readonly hidden>
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!($purchaseItemRow == null))
                                            @foreach ($oilAndGasPumpPurchase->purchaseItems as $perItemIndex => $perItem)
                                                <tr>
                                                    <td>{{ $perItemIndex +1 }}</td>
                                                    <td>
                                                        <select id="productInput{{ $perItemIndex }}" name="product[]" class="form-control form-select-sm @error('product.'.$perItemIndex) is-invalid @enderror" required>
                                                            <option value="">Select</option>
                                                            @foreach ($products as $perOilAndGasPumpProduct)
                                                                @php
                                                                    $productSlug = (old("product.".$perItemIndex) == null) ? $perItem->product->slug : old("product.".$perItemIndex);
                                                                @endphp
                                                                <option value="{{ $perOilAndGasPumpProduct->slug }}" {{ ($productSlug == $perOilAndGasPumpProduct->slug) ? "selected": null  }}>{{ $perOilAndGasPumpProduct->name }}</option>
                                                            @endforeach
                                                        </select>

                                                        @error('product.'.$perItemIndex)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input id="quantityInput{{ $perItemIndex }}" name="quantity[]" type="number" class="form-control form-control-sm @error('quantity.'.$perItemIndex) is-invalid @enderror" value="{{ (old('quantity.'.$perItemIndex) == null ) ?  $perItem->quantity : old('quantity.'.$perItemIndex) }}" min="0" step="1" required>
                                                        @error('quantity.'.$perItemIndex)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>

                                                    <td>
                                                        <input id="sellPriceInput{{ $perItemIndex }}" name="sell_price[]" type="number" class="form-control form-control-sm @error('sell_price.'.$perItemIndex) is-invalid @enderror" value="{{ (old('sell_price.'.$perItemIndex) == null ) ?  $perItem->sell_price : old('sell_price.'.$perItemIndex)}}" min="0" step="00.01" required>
                                                        @error('sell_price.'.$perItemIndex)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>

                                                    <td>
                                                        <input id="purchasePriceInput{{ $perItemIndex }}" name="purchase_price[]" type="number" class="form-control form-control-sm @error('purchase_price.'.$perItemIndex) is-invalid @enderror" value="{{ (old('purchase_price.'.$perItemIndex) == null ) ?  $perItem->purchase_price : old('purchase_price.'.$perItemIndex) }}" min="0" step="00.01" required>
                                                        @error('purchase_price.'.$perItemIndex)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>

                                                    <td>
                                                        <input id="discountInput{{ $perItemIndex }}" name="purchase_discount[]" type="number" class="form-control form-control-sm @error('purchase_discount.'.$perItemIndex) is-invalid @enderror" value="{{ (old('purchase_discount.'.$perItemIndex) == null ) ?  $perItem->discount : old('purchase_discount.'.$perItemIndex) }}" min="0" step="00.01" required>
                                                        @error('purchase_discount.'.$perItemIndex)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>

                                                    <td hidden>
                                                        <input id="rowTotalInput{{ $perItemIndex }}" name="total_purchase_price[]" type="number" class="form-control form-control-sm @error('total_purchase_price.'.$perItemIndex) is-invalid @enderror" value="{{ (old('total_purchase_price.'.$perItemIndex) == null) ? $perItem->totalPurchasePrice() : old('total_purchase_price.'.$perItemIndex)}}" min="0" step="00.01" required readonly hidden>
                                                        @error('total_purchase_price.'.$perItemIndex)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <div class="col-md-5 mb-2"></div>
                                <div class="col-md-7 mb-2">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Total amount ({{ $setting["businessSetting"]["currency_symbol"] }}) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                        <div class="col-md-8">
                                            <input id="totalPriceInput" name="total_price" type="number" class="form-control form-control-sm @error('total_price') is-invalid @enderror" value="{{ (old('total_price') == null) ? $oilAndGasPumpPurchase->totalPrice() : old('total_price') }}" min="0" step="00.01" required readonly>
                                            @error('total_price')
                                                <span class="invalid-feedback" role="alert" style="display: block;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 mb-2"></div>
                                <div class="col-md-7 mb-2">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Discount (%) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                        <div class="col-md-8">
                                            <input id="discountInput" name="discount" type="number" class="form-control form-control-sm @error('discount') is-invalid @enderror" value="{{ (old('discount') == null) ? $oilAndGasPumpPurchase->discount : old('discount') }}" min="0" step="00.01" required>
                                            @error('discount')
                                                <span class="invalid-feedback" role="alert" style="display: block;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 mb-2"></div>
                                <div class="col-md-7 mb-2">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Payable amount ({{ $setting["businessSetting"]["currency_symbol"] }}) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                        <div class="col-md-8">
                                            <input id="payableAmountInput" name="payable_amount" type="number" class="form-control form-control-sm @error('payable_amount') is-invalid @enderror" value="{{ (old('payable_amount') == null) ? $oilAndGasPumpPurchase->totalPayable() : old('payable_amount') }}" min="0" step="00.01" required readonly>
                                            @error('payable_amount')
                                                <span class="invalid-feedback" role="alert" style="display: block;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 mb-2"></div>
                                <div class="col-md-7 mb-2">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Paid amount ({{ $setting["businessSetting"]["currency_symbol"] }}) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                        <div class="col-md-8">
                                            <input id="paidAmountInput" name="paid_amount" type="number" class="form-control form-control-sm @error('paid_amount') is-invalid @enderror" value="{{ (old('paid_amount') == null) ? $oilAndGasPumpPurchase->totalPaid() : old('paid_amount') }}" min="0" step="00.01" required readonly>
                                            @error('paid_amount')
                                                <span class="invalid-feedback" role="alert" style="display: block;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 mb-2"></div>
                                <div class="col-md-7 mb-2">
                                    <div class="row">
                                        <label class="col-md-4 col-form-label col-form-label-sm text-bold">Due amount ({{ $setting["businessSetting"]["currency_symbol"] }}) <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                        <div class="col-md-8">
                                            <input id="dueAmountInput" name="due_amount" type="number" class="form-control form-control-sm @error('due_amount') is-invalid @enderror" value="{{ (old('due_amount') == null) ? $oilAndGasPumpPurchase->totalDue() : old('due_amount') }}" min="0" step="00.01" required readonly>
                                            @error('due_amount')
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
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Description</label>
                                <div class="col-lg-8">
                                    <textarea id="descriptionInput" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Ex: Hello">{{ (old('description') == null) ? $oilAndGasPumpPurchase->description : old('description') }}</textarea>

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

                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Status <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input id="statusInput" name="status" type="text" class="form-control form-control-sm @error('status') is-invalid @enderror" value="{{ (old('status') == null) ? $oilAndGasPumpPurchase->status : old('status') }}" placeholder="Ex: Due" readonly required>

                                    @error('status')
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
                <a role="button" href="{{ route("oil.and.gas.pump.purchase.index",["oagpSlug" =>  $oilAndGasPumpPurchase->oilAndGasPump->slug]) }}" class="btn btn-sm btn-secondary">
                    Go to oil and gas pump purchase
                </a>
            </div>
        </div>
    </div>
@endsection

@push('onPageExtraCss')
    <script>
        $(document).ready(function(){
            $(document).on('click', '#addRow', function () {
                var errorMessages = [];
                var minTableRow = "{{ $dataTableRow['min'] }}";
                var minTableRow = "{{ $dataTableRow['max'] }}";
                var tableRow = $("#tableRow").val();

                if((parseInt(tableRow) > 0) || (parseInt(tableRow) == 0)){
                    tableRow = parseInt(tableRow) + 1;
                    $("#tableRow").val(tableRow);

                    var row = "";
                    row = row + '<tr>';
                    row = row + '<td>' + tableRow + '</td>';
                    row = row + '<td><select id="productInput'+ tableRow +'" name="product[]" class="form-control form-select-sm " required><option value="">Select</option>@foreach ($products as $perOilAndGasPumpProduct)<option value="{{ $perOilAndGasPumpProduct->slug }}">{{ $perOilAndGasPumpProduct->name }}</option>@endforeach</select></td>';
                    row = row + '<td><input id="quantityInput'+ tableRow +'" name="quantity[]" type="number" class="form-control form-control-sm" value="0" min="0" step="1" required></td>';
                    row = row + '<td><input id="sellPriceInput'+ tableRow +'" name="sell_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required></td>';
                    row = row + '<td><input id="purchasePriceInput'+ tableRow +'" name="purchase_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required></td>';
                    row = row + '<td><input id="discountInput'+ tableRow +'" name="purchase_discount[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required></td>';
                    row = row + '<td hidden><input id="rowTotalInput'+ tableRow +'" name="total_purchase_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required readonly hidden></td>';
                    row = row + '</tr>';

                    $('#dataTable tbody').append(row);
                    rowButtonStatusChange();
                }
                filterProductForPurchaseProducts();
            });

            $(document).on('click', '#removeRow', function () {
                var minTableRow = "{{ $dataTableRow['min'] }}";
                var maxTableRow = "{{ $dataTableRow['max'] }}";
                var tableRow = $("#tableRow").val();

                if(parseInt(minTableRow) < parseInt(tableRow)){
                    tableRow = parseInt(tableRow) - 1;
                    $("#tableRow").val(tableRow);
                    $('#dataTable tbody tr:last').remove();

                    rowButtonStatusChange();
                    calculateTotalPurchaseAmount();
                }
                else{
                    rowButtonStatusChange();
                }
            });

            $("#dataTable tbody").on("change", 'input[name^="quantity"], input[name^="purchase_discount"], input[name^="purchase_price"]', function (event) {
                var currentValue = $(this).val();
                $(this).val(parseFloat(currentValue).toFixed(2));

                calculateRowTotal($(this).closest("tr"));
                calculateTotalPurchaseAmount();
            });

            $("#dataTable tbody").on("change", 'input[name^="sell_price"]', function (event) {
                var currentValue = $(this).val();
                $(this).val(parseFloat(currentValue).toFixed(2));
            });

            $("#dataTable tbody").on("change", 'select[name^="product"]', function (event) {
                filterProductForPurchaseProducts();
            });

            $(document).on('change', '#discountInput', function () {
                calculatePayAbleAmount();
                calculateDueAmount();
            });

            $(document).on('change', '#paidAmountInput', function () {
                calculateDueAmount();
            });

        });

        function rowButtonStatusChange(){
            var minTableRow = "{{ $dataTableRow['min'] }}";
            var maxCount = "{{ $dataTableRow['max'] }}";
            var tableRow = $("#tableRow").val();

            if(parseInt(tableRow) > 0 && (parseInt(tableRow) < parseInt(maxCount))){
                $("#addRow").prop("disabled",false);
                $("#addRow").prop("hidden",false);

                if(parseInt(tableRow) > minTableRow){
                    $("#removeRow").prop("disabled",false);
                    $("#removeRow").prop("hidden",false);
                }
                else{
                    $("#removeRow").prop("disabled",true);
                    $("#removeRow").prop("hidden",true);
                }
            }
            else{
                $("#addRow").prop("disabled",true);
                $("#addRow").prop("hidden",true);
            }
        }

        function showExtraErrorMessages(errorMessages,errorDivId){
            if(errorMessages.length > 0){
                var errorDivId = "#" + errorDivId;

                $(errorDivId).show();
                $(errorDivId).html("");
                $(errorDivId).html("<ul></ul>");

                $( errorMessages).each(function( index,perError ) {
                    $(errorDivId + " ul").append( "<li>" + perError + "</li>");
                });
            }
            else{
                $(errorDivId).hide();
                $(errorDivId).html("");
            }
        }

        function calculateRowTotal(row){
            var quantity = +row.find('input[name^="quantity"]').val();
            var purchaseDiscount = +row.find('input[name^="purchase_discount"]').val();
            var purchasePrice = +row.find('input[name^="purchase_price"]').val();

            var totalQuantityPurachecPrice = parseFloat(purchasePrice) * parseFloat(quantity);
            var totalQuantityProductDiscount = parseFloat(totalQuantityPurachecPrice) * (parseFloat(purchaseDiscount)/100);

            var totalPurchasePrice = (totalQuantityPurachecPrice - totalQuantityProductDiscount).toFixed(2);

            row.find('input[name^="total_purchase_price"]').val(totalPurchasePrice);
            calculateTotalPrice();
        }

        function calculateTotalPrice(){
            var totalPrice = 0;

            var rowTotalPrice = $('#dataTable').find('input[name^="total_purchase_price"]').val();

            $("#dataTable").find('input[name^="total_purchase_price"]').each(function () {
                totalPrice = parseFloat(totalPrice) + parseFloat($(this).val());
            });
            $("#totalPriceInput").val(totalPrice.toFixed(2));
        }

        function calculatePayAbleAmount(){
            $('#discountInput').val(parseFloat($('#discountInput').val()).toFixed(2));

            var discount = $('#discountInput').val();
            var totalPrice = $("#totalPriceInput").val();

            var totalDiscount =  parseFloat(totalPrice) * (parseFloat(discount)/100);

            var payableAmount = totalPrice - totalDiscount;
            $("#payableAmountInput").val(payableAmount.toFixed(2));
        }

        function calculateDueAmount(){
            var status = "Due";
                $('#paidAmountInput').val(parseFloat($('#paidAmountInput').val()).toFixed(2));

                var paidAmount = $('#paidAmountInput').val();
                var payableAmount = $("#payableAmountInput").val();

                var dueAmount = parseFloat(payableAmount) - parseFloat(paidAmount);
                $("#dueAmountInput").val(dueAmount.toFixed(2));

                if(dueAmount == 0){
                    status = "Complete";
                }
                if(dueAmount > 0){
                    status = "Due";
                }
                $("#statusInput").val(status);
        }

        function calculateTotalPurchaseAmount(){
            calculateTotalPrice();
            calculatePayAbleAmount();
            calculateDueAmount();
        }

        function filterProductForPurchaseProducts(){
            var selectedProducts = [];
            $("#dataTable tbody").find('select[name^="product"]').each(function () {
                var productValue = $(this).val();
                if(productValue.length > 0 ){
                    selectedProducts.push(productValue);
                }
            });

            if(selectedProducts.length > 0){
                $.ajax({
                    type:'get',
                    url:"{{ route('oil.and.gas.pump.purchase.get.product',['oagpSlug'=>$oilAndGasPumpPurchase->oilAndGasPump->slug]) }}",
                    data:{"selected_products":selectedProducts},
                    success:function(successResponce){
                        $("#dataTable tbody").find('select[name^="product"]').each(function () {
                            if(($(this).val() == null) || ($(this).val().length == 0) ){
                                var filterableProductId = $(this).attr("id");

                                $(this).empty();
                                $(this).append('<option value="">Select</option>');

                                $.each(successResponce, function( index, perValue ) {
                                    $("#dataTable tbody #"+filterableProductId).append('<option value="'+ perValue.slug +'">'+ perValue.name +'</option>');
                                });
                            }
                        });
                    }
                });
            }
        }
    </script>
@endpush
