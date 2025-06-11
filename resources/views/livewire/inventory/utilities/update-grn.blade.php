<div>
    <div class="box box-primary">
        <div class="box-header with-border">
            @include('message')
            <h4 class="box-title">Update GRN</h4>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group  @error('grnNumber') is-invalid @enderror">
                        <label for="grn_number">GRN Number</label>
                        <input type="grn_number" wire:model="grnNumber" class="form-control">
                        @error('grnNumber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-3">
                    <label style="display: block">&nbsp;</label>
                    <button class="btn btn-primary" wire:click="loadGrn">
                        <i class="fa fa-search"></i>
                        Load
                    </button>
                </div>
            </div>
        </div>
    </div>
    @if ($grnNumber)
        <form wire:submit="save">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4 class="box-title" style="margin-bottom: 10px">Purchase No: {{ $purchaseNo }}</h4> <br>
                    <strong>Supplier: {{ $supplier }}</strong>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class = "row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Supplier Invoice No.</label>
                                    <div class="col-sm-7">
                                        <input type="text" wire:model="supplierInvoiceNumber" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class = "row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">CU Invoice Number</label>
                                    <div class="col-sm-7">
                                        <input type="text" wire:model="cuInvoiceNumber" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4>Items</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item No</th>
                                <th colspan="2">Description</th>
                                <th>QTY</th>
                                <th>Price</th>
                                <th>VAT</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($grnItems as $index => $grnItem)
                                <tr>
                                    <td>{{ $grnItem->item_code }}</td>
                                    <td colspan="2">{{ $grnItem->description }}</td>
                                    <td style="width: 200px">
                                        @if (can('change-qty', 'update-grn-utility'))
                                            <input type="number" class="form-control"
                                                wire:model.lazy="grnItems.{{ $index }}.qty" step="any">
                                        @else
                                            {{ $grnItem->qty }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (can('change-price', 'update-grn-utility'))
                                            <input type="number" class="form-control"
                                                wire:model.lazy="grnItems.{{ $index }}.price" step="any">
                                        @else
                                            {{ manageAmountFormat($grnItem->price) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (can('change-price', 'update-grn-utility'))
                                            <input type="number" class="form-control"
                                                wire:model.lazy="grnItems.{{ $index }}.vat_rate" step="any">
                                        @else
                                            {{ manageAmountFormat($grnItem->vat_rate) }}
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        {{ manageAmountFormat($grnItem->current->total_amount) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="7"></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <table class="table">
                                        <tr>
                                            <th>Current</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th>Gross AMount</th>
                                            <td class="text-right">
                                                {{ manageAmountFormat($current->total_exclusive_amount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Vat Amount</th>
                                            <td class="text-right">
                                                {{ manageAmountFormat($current->total_vat_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Discount</th>
                                            <td class="text-right">
                                                {{ manageAmountFormat($current->total_discount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Total Value</th>
                                            <td class="text-right">
                                                {{ manageAmountFormat($current->total_amount) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="3">
                                    <table class="table">
                                        <tr>
                                            <th class="text-left">Adjusted</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">Gross Amount</th>
                                            <td class="text-right">
                                                {{ manageAmountFormat($adjusted->total_exclusive_amount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">Vat Amount</th>
                                            <td class="text-right">
                                                {{ manageAmountFormat($adjusted->total_vat_amount) }}
                                        </tr>
                                        <tr>
                                            <th class="text-left">Total Discount</th>
                                            <td class="text-right">
                                                {{ manageAmountFormat($adjusted->total_discount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">Total Value</th>
                                            <td class="text-right">
                                                {{ manageAmountFormat($adjusted->total_amount) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="text-right">
                        <button type="submit" class="btn btn-secondary">
                            &times; Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save
                            <i wire:loading class="fa fa-spinner fa-spin"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
