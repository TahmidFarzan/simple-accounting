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
            <li class="breadcrumb-item"><a href="{{ route("oil.and.gas.pump.details",["slug" => $oilAndGasPump->slug]) }}">{{ $oilAndGasPump->name }}</a></li>
            <li class="breadcrumb-item">Purchase</li>
            <li class="breadcrumb-item active" aria-current="page">Index</li>
        </ol>
    </nav>
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('authContentOne')

    @php
        $dataTableRow = array('min' => 1,'max' => 5);
    @endphp
    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("oil.and.gas.pump.purchase.save",["oagpSlug" => $oilAndGasPump->slug]) }}" method="POST" id="createForm">
                @csrf

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-md-4 col-form-label col-form-label-sm text-bold">Date <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-8">
                                    <input id="dateInput" name="date" type="date" class="form-control form-control-sm @error('date') is-invalid @enderror" value="{{ date('Y-m-d', strtotime(now())) }}" max="{{ date('Y-m-d', strtotime(now())) }}" required>
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
                                    <input id="invoiceInput" name="invoice" type="text" class="form-control form-control-sm @error('invoice') is-invalid @enderror" value="{{ old('invoice') }}" placeholder="Ex: 15663" maxlength="200" required>
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
                                    <select id="supplierInput" name="supplier" class="form-control form-control-sm @error('supplier') is-invalid @enderror">
                                        <option value="">Select</option>
                                        @foreach ($oagpSuppliers as $perSupplier)
                                        <option value="{{ $perSupplier->slug }}">{{ $perSupplier->name }}</option>
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
                                    <input id="nameInput" name="name" type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ex: Hello" maxlength="200" required>
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
                                    <input id="rowCount" name="row_count" type="number" class="form-control form-control-sm" value="{{ (old("table_row") == null) ? $dataTableRow["min"] : old("table_row") }}" min="{{ $dataTableRow["min"] }}" max="{{ $dataTableRow["max"] }}" required readonly hidden>
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
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Purchase price ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                            <th>Discount (%)</th>
                                            <th>Sell price ({{ $setting["businessSetting"]["currency_symbol"] }})</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (old("table_row") == null)
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <select id="productInput1" name="product[]" class="form-control form-select-sm " required>
                                                        <option value="">Select</option>
                                                        @foreach ($oilAndGasPumpProducts as $perOilAndGasPumpProduct)
                                                            <option value="{{ $perOilAndGasPumpProduct->slug }}">{{ $perOilAndGasPumpProduct->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="quantityInput1" name="quantity[]" type="number" class="form-control form-control-sm" value="0" min="0" step="1" required>
                                                </td>
                                                <td>
                                                    <input id="purchasePriceInput1" name="purchase_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required>
                                                </td>
                                                <td>
                                                    <input id="discountInput1" name="discount[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required>
                                                </td>
                                                <td>
                                                    <input id="sellPriceInput1" name="sell_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required>
                                                </td>
                                                <td>
                                                    <input id="rowTotalInput1" name="row_total[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required readonly>
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!(old("table_row") == null))
                                            @for ($i = 0 ;$i <old("table_row") ; $i++)
                                                <tr>
                                                    <td>{{ $i +1 }}</td>
                                                    <td>
                                                        <select id="productInput{{ $i }}" name="product[]" class="form-control form-select-sm @error('product.'.$i) is-invalid @enderror" required>
                                                            <option value="">Select</option>
                                                            @foreach ($oilAndGasPumpProducts as $perOilAndGasPumpProduct)
                                                                    <option value="{{ $perOilAndGasPumpProduct->slug }}" {{ (old("product.".$i) == $perOilAndGasPumpProduct->slug) ? "selected": null  }}>{{ $perOilAndGasPumpProduct->name }}</option>
                                                            @endforeach
                                                        </select>

                                                        @error('product.'.$i)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input id="quantityInput{{ $i }}" name="quantity[]" type="number" class="form-control form-control-sm @error('quantity.'.$i) is-invalid @enderror" value="{{ old('quantity.'.$i) }}" min="0" step="1" required>
                                                        @error('quantity.'.$i)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input id="purchasePriceInput{{ $i }}" name="purchase_price[]" type="number" class="form-control form-control-sm @error('purchase_price.'.$i) is-invalid @enderror" value="{{ old('purchase_price.'.$i) }}" min="0" step="00.01" required>
                                                        @error('purchase_price.'.$i)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input id="discountInput{{ $i }}" name="discount[]" type="number" class="form-control form-control-sm @error('discount.'.$i) is-invalid @enderror" value="{{ old('discount.'.$i) }}" min="0" step="00.01" required>
                                                        @error('discount.'.$i)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input id="sellPriceInput{{ $i }}" name="sell_price[]" type="number" class="form-control form-control-sm @error('sell_price.'.$i) is-invalid @enderror" value="{{ old('sell_price.'.$i) }}" min="0" step="00.01" required>
                                                        @error('sell_price.'.$i)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input id="rowTotalInput{{ $i }}" name="row_total[]" type="number" class="form-control form-control-sm @error('row_total.'.$i) is-invalid @enderror" value="{{ old('row_total.'.$i) }}" min="0" step="00.01" required readonly>
                                                        @error('row_total.'.$i)
                                                            <span class="invalid-feedback" role="alert" style="display: block;">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endfor
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
                                            <input id="totalPriceInput" name="total_price" type="number" class="form-control form-control-sm @error('total_price') is-invalid @enderror" value="{{ (old('total_price') == null) ? 0 : old('total_price') }}" min="0" step="00.01" required readonly>
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
                                            <input id="discountInput" name="discount" type="number" class="form-control form-control-sm @error('discount') is-invalid @enderror" value="{{ (old('discount') == null) ? 0 : old('discount') }}" min="0" step="00.01" required>
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
                                            <input id="payableAmountInput" name="payable_amount" type="number" class="form-control form-control-sm @error('payable_amount') is-invalid @enderror" value="{{ (old('payable_amount') == null) ? 0 : old('payable_amount') }}" min="0" step="00.01" required readonly>
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
                                            <input id="paidAmountInput" name="paid_amount" type="number" class="form-control form-control-sm @error('paid_amount') is-invalid @enderror" value="{{ (old('paid_amount') == null) ? 0 : old('paid_amount') }}" min="0" step="00.01" required>
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
                                            <input id="dueAmountInput" name="due_amount" type="number" class="form-control form-control-sm @error('due_amount') is-invalid @enderror" value="{{ (old('due_amount') == null) ? 0 : old('due_amount') }}" min="0" step="00.01" required readonly>
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
                                    <textarea id="descriptionInput" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Ex: Hello">{{ old('description') }}</textarea>

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
                <a role="button" href="{{ route("oil.and.gas.pump.purchase.index",["oagpSlug" => $oilAndGasPump->slug]) }}" class="btn btn-sm btn-secondary">
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
                var minRowCount = "{{ $dataTableRow['min'] }}";
                var minRowCount = "{{ $dataTableRow['max'] }}";
                var rowCount = $("#rowCount").val();

                if((parseInt(rowCount) > 0) || (parseInt(rowCount) == 0)){
                    rowCount = parseInt(rowCount) + 1;
                    $("#rowCount").val(rowCount);

                    var row = "";
                    row = row + '<tr>';
                    row = row + '<td>' + rowCount + '</td>';
                    row = row + '<td><select id="productInput'+ rowCount +'" name="product[]" class="form-control form-select-sm " required><option value="">Select</option>@foreach ($oilAndGasPumpProducts as $perOilAndGasPumpProduct)<option value="{{ $perOilAndGasPumpProduct->slug }}">{{ $perOilAndGasPumpProduct->name }}</option>@endforeach</select></td>';
                    row = row + '<td><input id="quantityInput'+ rowCount +'" name="quantity[]" type="number" class="form-control form-control-sm" value="0" min="0" step="1" required></td>';
                    row = row + '<td><input id="purchasePriceInput'+ rowCount +'" name="purchase_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required></td>';
                    row = row + '<td><input id="discountInput'+ rowCount +'" name="discount[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required></td>';
                    row = row + '<td><input id="sellPriceInput'+ rowCount +'" name="sell_price[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required></td>';
                    row = row + '<td><input id="rowTotalInput'+ rowCount +'" name="row_total[]" type="number" class="form-control form-control-sm" value="0" min="0" step="00.01" required readonly></td>';
                    row = row + '</tr>';

                    $('#dataTable tbody').append(row);
                    rowButtonStatusChange();
                }
            });

            $(document).on('click', '#removeRow', function () {
                var minRowCount = "{{ $dataTableRow['min'] }}";
                var maxRowCount = "{{ $dataTableRow['max'] }}";
                var rowCount = $("#rowCount").val();

                if(parseInt(minRowCount) < parseInt(rowCount)){
                    rowCount = parseInt(rowCount) - 1;
                    $("#rowCount").val(rowCount);
                    $('#dataTable tbody tr:last').remove();

                    rowButtonStatusChange();
                    calculateTotalPrice();
                }
                else{
                    rowButtonStatusChange();
                }
            });

            $("#dataTable tbody").on("change", 'input[name^="quantity"], input[name^="discount"], input[name^="purchase_price"]', function (event) {
                calculateRowTotal($(this).closest("tr"));
            });

            $(document).on('change', '#discountInput', function () {
                var totalPrice = $("#totalPriceInput").val();
                var discount = $("#discountInput").val();
                var totalDiscount =  parseFloat(totalPrice) * (parseFloat(discount)/100);

                var payableAmount = totalPrice - totalDiscount;
                $("#payableAmountInput").val(payableAmount.toFixed(2));
            });

            $(document).on('change', '#paidAmountInput', function () {
                var payableAmount = $("#payableAmountInput").val();
                var paidAmount = $("#paidAmountInput").val();

                var dueAmount = parseFloat(payableAmount) - parseFloat(paidAmount);
                $("#dueAmountInput").val(dueAmount.toFixed(2));
            });
        });

        function rowButtonStatusChange(){
            var minRowCount = "{{ $dataTableRow['min'] }}";
            var maxCount = "{{ $dataTableRow['max'] }}";
            var rowCount = $("#rowCount").val();

            if(parseInt(rowCount) > 0 && (parseInt(rowCount) < parseInt(maxCount))){
                $("#addRow").prop("disabled",false);
                $("#addRow").prop("hidden",false);

                if(parseInt(rowCount) > 1){
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
            var rowTotal = 0;
            var quantity = +row.find('input[name^="quantity"]').val();
            var discount = +row.find('input[name^="discount"]').val();
            var purchasePrice = +row.find('input[name^="purchase_price"]').val();

            var totalPurachecPrice = parseFloat(purchasePrice) * parseFloat(quantity);
            var totalRowDiscount = parseFloat(totalPurachecPrice) * (parseFloat(discount)/100);
            rowTotal = (totalPurachecPrice - totalRowDiscount).toFixed(2);

            row.find('input[name^="row_total"]').val(rowTotal);
            calculateTotalPrice();
        }

        function calculateTotalPrice(){
            var totalPrice = 0;

            var rowTotalPrice = $('#dataTable').find('input[name^="row_total"]').val();

            $("#dataTable").find('input[name^="row_total"]').each(function () {
                totalPrice = parseFloat(totalPrice) + parseFloat($(this).val());
            });
            $("#totalPriceInput").val(totalPrice.toFixed(2));
        }
    </script>
@endpush
