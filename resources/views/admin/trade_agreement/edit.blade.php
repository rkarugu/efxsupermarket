@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Trade Agreement </h3>

            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>Supplier</th>
                                <td>{{ $trade->supplier->supplier_code }} / {{ $trade->supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>Reference</th>
                                <td>{{ $trade->reference }}</td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td>{{ date('d M Y', strtotime($trade->date)) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <div style="text-align: right;width:100%">
                            @if (can('lock', 'trade-agreement'))
                                <span class='span-action' style="font-size: 25px; display: inline-block; vertical-align: middle;">
                                    <button class="btn lock-btn"
                                        onclick="openTheLockModal('{{ route('trade-agreement.lock_agreement', $trade->id) }}?editing=1', '{{ $trade->is_locked ? 'Un Lock' : 'Lock' }}' ,'{!! $trade->reference !!}'); return false;"
                                        style="background-color: {{ $trade->is_locked ? 'green' : 'red' }}; color: white; border: none; padding: 10px; cursor: pointer;height: 35px;">
                                        @if ($trade->is_locked)
                                            <i class="fa fa-lock" style="color:black;"></i>
                                        @else
                                            <i class="fa fa-unlock" style="color:black;"></i>
                                        @endif
                                    </button>
                                </span>
                            @endif
                            @if ($trade->status == 'Approved')
                                <a href="{{ route($model . '.get_document', $trade->id) }}" class="btn btn-primary">
                                    <i class="fa fa-download"></i> Download Trade Agreement
                                </a>
                            @endif
                        </div>
                        @if ($trade->status == 'Pending')
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modelId">
                                Approve/Reject
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="modelId" tabindex="-1" role="dialog"
                                aria-labelledby="modelTitleId" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route($model . '.update', $trade->id) }}" method="post"
                                            class="submitMe">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h3 class="modal-title">Update Agreement Request Status</h3>

                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="">Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="Approved" selected>Approve</option>
                                                        <option value="Rejected">Reject</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Comment</label>
                                                    <textarea class="form-control" name="comment" id="commnet" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i> Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @if ($trade->status == 'Approved')
                    <div class="row">
                        <div class="col-sm-12">
                            @if (!$trade->is_locked)
                                <form action="{{ route('trade-agreement.summary_update', $trade->id) }}" method="post"
                                    class="submitMe">
                                    @csrf
                                    @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <th>Quarterly Cycle Start</th>
                                            <td>
                                                <select name="quarterly_cycle_start" id="quarterly_cycle_start"
                                                    class="form-control">
                                                    <option value="">Select Option</option>
                                                    @foreach ($months as $month)
                                                        <option value="{{ $month }}" @selected($month == $trade->quarterly_cycle_start)>
                                                            {{ $month }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <span name="summary"></span>
                            <table id="add_summary" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            Agreement Summary
                                        </th>
                                        @if (!$trade->is_locked)
                                            <th>
                                                <button class="btn btn-primary btn-sm" type="button"
                                                    onclick="addMoreRows(); return false;">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                @php
                                    $summary = json_decode($trade->summary);
                                @endphp
                                <tbody>
                                    @if ($summary && count($summary) > 0)
                                        @foreach ($summary as $key => $item)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $item }}"
                                                        name="summary[]">
                                                </td>
                                                @if (!$trade->is_locked)
                                                    <td>
                                                        <a href="#" class="remove-btn">
                                                            <i class="fa fa-trash" style="margin-top: 10px"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @if (!$trade->is_locked)
                                <button type="submit" class="btn btn-danger"> <i class="fa-solid fa-pen-to-square"></i>
                                    Update @if ($trade->linked_to_portal)
                                        and send to supplier
                                    @endif
                                </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($trade->status == 'Approved')
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3 class="box-title"> Trade Agreement Discounts </h3>
                        </div>
                        <div class="col-sm-4 text-right">

                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary  close_discount_modal" data-toggle="modal"
                                data-target="#discount_settings"
                                @if ($trade->is_locked) style="display:none" @endif>
                                <i class="fa fa-plus"></i> Add Discount
                            </button>

                        </div>
                    </div>

                </div>
                <!-- Modal -->
                <div class="modal fade" id="discount_settings" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <form action="{{ route('trade-agreement.store_discount', $trade->id) }}" method="post"
                        class="submitPerform submitMe">
                        @csrf
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Discount</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="">Discount Type</label>
                                        <select name="discount_type" onchange="show_discount_form(this)"
                                            class="form-control discount_type" id="discount_type">
                                            <option value="" selected disabled>-- Select Discount Type --</option>
                                            @foreach ($discount_types as $key => $type)
                                                <option value="{{ $key }}">{{ $type['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="add_form_here"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    @if (!$trade->is_locked)
                                        <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> &nbsp;
                                            Save Discount</button>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>Discount</th>
                            <th>Target</th>
                            <th>Application Stage</th>
                            <th>Period</th>
                            {{-- <th>Discount Value</th> --}}
                            <th>No of Products</th>
                            <th></th>
                        </tr>
                        @forelse ($discounts as $discount)
                            <tr>
                                <td>
                                    {{ @$discount_types[$discount->discount_type]['name'] }}
                                </td>
                                <td>
                                    {{ in_array($discount->discount_type, ['Invoice Discount', 'No Goods Return Discount']) ? 'Invoice' : 'Product' }}
                                </td>
                                <td>
                                    {{ @$discount_types[$discount->discount_type]['stage'] }}
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    @if (in_array($discount->discount_type, [
                                            'Base Discount',
                                            'Invoice Discount',
                                            'Purchase Quantity Offer',
                                            'Target discount on value',
                                            'Target discount on quantity',
                                            'End month Discount',
                                            'Quarterly Discount',
                                        ]))
                                        @php
                                            $other_options = count((array) json_decode($discount->other_options));
                                        @endphp
                                        {{ $other_options }}
                                    @endif
                                </td>

                                <td>
                                    <a href="#" onclick="opendiscountmodel({{ $discount->id }}); return false;">
                                        @if (!$trade->is_locked)
                                        <i class="fa fa-pencil"></i>@else<i class="fa fa-eye"></i>
                                        @endif
                                    </a>
                                    @if (!$trade->is_locked)
                                        <!-- Button trigger modal -->
                                        {{-- <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#modelId{{ $discount->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button> --}}

                                        <a href="#" data-toggle="modal" data-target="#modelId{{ $discount->id }}">
                                            <i class="fa fa-trash"></i>
                                        </a>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modelId{{ $discount->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                            <form action="{{ route('trade-agreement.delete_discount', $discount->id) }}"
                                                class="submitMe" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title delete-title">Confirm Delete Discount
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="delete-p"> You are deleting
                                                                {{ @$discount_types[$discount->discount_type]['name'] }}
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>

                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="fa fa-trash"></i> &nbsp; Confirm and
                                                                Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No records found</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </section>

        <section class="content" data-id="Offer Page">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-sm-4">
                            <h3 class="box-title">Listed Products</h3>
                        </div>

                        <div class="col-sm-4 text-right">
                            <form id="download-trade-agreement-items"
                                action="{{ route('download_trade_agreement_items') }}" method="post">
                                @csrf
                                <input type="hidden" name="trade_agreement_id" value="{{ $trade->id }}">
                                <button type="submit" class="btn btn-primary" name="intent" id="download-pdf"
                                    value="Download Pdf">
                                    <i class="fa fa-file-pdf"></i> Download PDF
                                </button>
                                <button type="submit" class="btn btn-primary" name="intent" id="download-excel"
                                    value="Download Excel">
                                    <i class="fa fa-file-excel"></i> Download Excel
                                </button>
                            </form>
                        </div>

                        <div class="col-sm-4 text-right">
                            {{-- @if (!$trade->is_locked) --}}
                            <button type="button" class="btn btn-primary" id="open_update_modal_btn">
                                <i class="fa-solid fa-pen-to-square"></i> Update
                            </button>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#openAddProductOfferModal">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#openAllProductOfferModal">
                                <i class="fa-solid fa-list"></i> &nbsp; List All Products
                            </button>
                            {{-- @endif --}}
                        </div>


                    </div>
                </div>
                <div class="box-body">
                    <table class="table" id="productsDataTable">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>UOM</th>
                                <th>Price List Cost</th>
                                <th>Base Disc.</th>
                                <th>Invoice Disc.</th>
                                <th>Standard Cost</th>
                                <th>Selling Price</th>
                                <th>Margin Type</th>
                                <th>Margin Value</th>
                                <th>Trade Agreement Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="openProductOfferModal">
                @if (!$trade->is_locked)
                    <form action="{{ route('trade-agreement.store_offer_amount', $trade->id) }}"
                        class="submitMe addProductOfferModal" method="post">
                        @csrf
                @endif
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Set Product Target</h5>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="">Product</label>
                                <input type="text" name="product_name" value="Product Name" readonly
                                    class="product_name form-control">
                                <input type="hidden" name="stock_id_code" value="">
                                <input type="hidden" name="inventory_item_id" value="">
                            </div>

                            <div class="form-group">
                                <label for="">Offer Amount KES</label>
                                <input type="text" name="offer_amount" placeholder="KES"
                                    class="form-control offer_amount">
                            </div>
                            <div class="form-group">
                                <label for="">Target Quantity</label>
                                <input type="text" name="target_quantity" placeholder="0.00"
                                    class="form-control target_quantity">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            @if (!$trade->is_locked)
                                <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> &nbsp;
                                    Save</button>
                            @endif
                        </div>
                    </div>
                </div>
                @if (!$trade->is_locked)
                    </form>
                @endif
            </div>

            <!-- Modal -->
            <div class="modal fade" id="openAddProductOfferModal">
                {{-- @if (!$trade->is_locked) --}}
                <form action="{{ route('trade-agreement.store_offer_amount', $trade->id) }}"
                    class="submitMe openAddProductOfferModal" method="post">
                    {{-- @endif --}}
                    @csrf
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Link New Product to Trade Agreement</h5>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="">Product</label>
                                    <select name="inventory_item_id"
                                        class="form-control open-select2 inventory_item_select">
                                        <option value="" selected disabled>--Select Product--</option>
                                        @foreach ($allProducts as $item)
                                            <option value="{{ $item->id }}"
                                                data-stock_id="{{ $item->stock_id_code }}">
                                                {{ $item->stock_id_code }} - {{ $item->title }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="stock_id_code" value="">
                                    <input type="hidden" name="offer_amount" placeholder="KES"
                                        class="form-control offer_amount" value="0">
                                    <input type="hidden" name="target_quantity" placeholder="0.00"
                                        class="form-control target_quantity" value="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                {{-- @if (!$trade->is_locked) --}}
                                <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> &nbsp;
                                    Save</button>
                                {{-- @endif --}}
                            </div>
                        </div>
                    </div>
                    {{-- @if (!$trade->is_locked) --}}
                </form>
                {{-- @endif --}}
            </div>

            <div class="modal fade" id="openAllProductOfferModal">
                {{-- @if (!$trade->is_locked) --}}
                <form action="{{ route('trade-agreement.store_all_offer_amount', $trade->id) }}"
                    class="submitMe openAllProductOfferModal" method="post">
                    {{-- @endif --}}
                    @csrf
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">List All Products</h5>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="">Product</label>
                                    <input type="text" name="product_name" value="All Products" readonly
                                        class="form-control">
                                    <input type="hidden" name="offer_amount" placeholder="KES" class="form-control"
                                        value="0">
                                    <input type="hidden" name="target_quantity" placeholder="0.00" class="form-control"
                                        value="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                {{-- @if (!$trade->is_locked) --}}
                                <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> &nbsp;
                                    Save</button>
                                {{-- @endif --}}
                            </div>
                        </div>
                    </div>
                    {{-- @if (!$trade->is_locked) --}}
                </form>
                {{-- @endif --}}
            </div>

            {{-- Retire modal start --}}

            <div class="modal fade" id="retireItemModal">
                <form action="" class="submitMe retireItemModal" method="post">
                    @csrf
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Retire item</h5>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="retire-item-btn">
                                    <i class="fa fa-times"></i> Retire
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Retire modal end --}}


            {{-- Update Item Price List Cost Modal Start --}}


            <div class="modal fade" id="openUpdateItemsModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title update-title">Update Item Price List Cost</h5>
                        </div>
                        <div class="modal-body">
                            <table class="table" id="updateItemsTable">
                                <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Price List Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update-item-cost">
                                <i class="fa-solid fa-pen-to-square"></i> Update</button>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Update Item Price List Cost Modal End --}}

        </section>

        <div class="discount_form_items">
            <div style="display:none" data-id="Base Discount" class="discount_form">
                <div>
                    <p>Products get discounted at
                        LPO processing.</p>

                    <div class="form-group show_hide_discount_value">
                        <label for="discount_value">Enter
                            Discount
                            Percentage</label>
                        <input type="number" name="discount_value" class="discount_value form-control">
                    </div>

                    <div class="form-group">
                        <label for="applies_to_all_item">
                            <input type="checkbox" name="applies_to_all_item" value="1"
                                onchange="base_invoice_applies_to_all_item(this)" class="applies_to_all_item">
                            Discount
                            applies to all products</label>
                    </div>
                    <div class="form-group show_hide_select_products">
                        <div class="form-group">
                            <label for="">Select Product</label>
                            <select name="select-products" class="form-control select-products">
                                <option value="" selected disabled>-- Select Product --</option>
                                <option value="All">Select All</option>
                                @foreach ($inventory as $key => $item)
                                    <option value='{{ $item->id }}' data-stock_id="{{ $item->stock_id_code }}">
                                        {{ $item->title }} {{ $item->net_weight }} KG</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="table table-bordered table-hover selected-product-list">
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        No. of Products: <span class="no_of_products_selected"></span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Discount</th>
                                    <th>Type</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div style="display:none" data-id="Bank Guarantee Discount" class="discount_form">
                <div>
                    <p>Products get discounted at
                        LPO processing.</p>

                    <div class="form-group show_hide_discount_value">
                        <label for="discount_value">Enter
                            Discount
                            Percentage</label>
                        <input type="number" name="discount_value" class="discount_value form-control">
                    </div>

                    <div class="form-group">
                        <label for="applies_to_all_item">
                            <input type="checkbox" name="applies_to_all_item" value="1"
                                onchange="base_invoice_applies_to_all_item(this)" class="applies_to_all_item">
                            Discount
                            applies to all products</label>
                    </div>
                    <div class="form-group show_hide_select_products">
                        <div class="form-group">
                            <label for="">Select Product</label>
                            <select name="select-products" class="form-control select-products">
                                <option value="" selected disabled>-- Select Product --</option>
                                <option value="All">Select All</option>
                                @foreach ($inventory as $key => $item)
                                    <option value='{{ $item->id }}' data-stock_id="{{ $item->stock_id_code }}">
                                        {{ $item->title }} {{ $item->net_weight }} KG</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="table table-bordered table-hover selected-product-list">
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        No. of Products: <span class="no_of_products_selected"></span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Discount</th>
                                    <th>Type</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div style="display:none" data-id="Invoice Discount" class="discount_form">
                <div>
                    <p>Invoices get discounted at
                        LPO
                        processing.</p>
                    <div class="form-group show_hide_discount_value">
                        <label for="discount_value">Enter
                            Discount
                            Percentage</label>
                        <input type="number" name="discount_value" class="discount_value form-control">
                    </div>

                    <div class="form-group">
                        <label for="applies_to_all_item">
                            <input type="checkbox" name="applies_to_all_item" value="1"
                                onchange="base_invoice_applies_to_all_item(this)" class="applies_to_all_item">
                            Discount
                            applies to all products</label>
                    </div>
                    <div class="form-group show_hide_select_products">
                        <div class="form-group">
                            <label for="">Select Product</label>
                            <select name="select-products" class="form-control select-products">
                                <option value="" selected disabled>-- Select Product --</option>
                                <option value="All">Select All</option>
                                @foreach ($inventory as $key => $item)
                                    <option value='{{ $item->id }}' data-stock_id="{{ $item->stock_id_code }}">
                                        {{ $item->title }} {{ $item->net_weight }} KG</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="table table-bordered table-hover selected-product-list">
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        No. of Products: <span class="no_of_products_selected"></span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Discount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div style="display:none" data-id="No Goods Return Discount" class="discount_form">
                <div>
                    <p>Invoices get discounted at
                        LPO
                        processing.</p>

                    <div class="">
                        <div class="form-group">
                            <label for="discount_value">Enter
                                Discount
                                Percentage</label>
                            <input type="text" name="discount_value" class="discount_value form-control">

                        </div>
                    </div>
                </div>
            </div>

            <div style="display:none" data-id="Purchase Quantity Offer" class="discount_form">
                <div>
                    <p>Discount targets products
                        during LPO processing, providing complimentary items upon
                        purchasing a specified quantity of the product</p>


                    <div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="entered_purchased_quantity">Enter
                                        Purchase Quantity</label>
                                    <input type="number" name="entered_purchased_quantity"
                                        class="entered_purchased_quantity form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="entered_value">Enter
                                        Value</label>
                                    <input type="number" name="entered_value" class="entered_value form-control">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="applies_to_all_item">
                                <input type="checkbox" name="applies_to_all_item" value="1"
                                    onchange="target_value_applies_to_all_item(this)" class="applies_to_all_item">
                                Applies to all products</label>
                        </div>
                        {{-- <div class="form-group">
                        <label for="applies_to_all_item"
                            >
                        <input type="checkbox" name="applies_to_all_item"
                             value="1" onchange="show_hide_select_products(this)"
                            class="applies_to_all_item">
                        Discount
                            applies to all products</label>
                    </div> --}}
                        <div class="form-group show_hide_select_products">
                            <div class="form-group">
                                <label for="">Select Product</label>
                                <select name="select-products" class="form-control select-products">
                                    <option value="" selected disabled>-- Select Product --</option>
                                    <option value="All">Select All</option>
                                    @foreach ($inventory as $key => $item)
                                        <option value='{{ $item->id }}' data-stock_id="{{ $item->stock_id_code }}">
                                            {{ $item->title }} {{ $item->net_weight }} KG</option>
                                    @endforeach
                                </select>
                            </div>
                            <table class="table table-bordered table-hover selected-product-list">
                                <thead>
                                    <tr>
                                        <th colspan="5">
                                            No. of Products: <span class="no_of_products_selected"></span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Purchase Quantity</th>
                                        <th>Value</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>

                        {{-- <div class="form-group">
                        <label for="purchased_product_quantity"
                            >Quantity
                            of products to be purchased</label>
                        <input type="number" name="purchased_product_quantity"
                            class="purchased_product_quantity form-control">
                    </div>
    
                    <div class="">
                        <label for="free_product_quantity"
                            >Quantity
                            of free products</label>
                        <input type="number" id="free_product_quantity" name="free_product_quantity"
                            class="free_product_quantity form-control">
                    </div> --}}
                    </div>
                </div>
            </div>

            <div style="display:none" data-id="Payment Discount" class="discount_form">
                <div>
                    <p>Invoices are discounted when
                        payment is made within a specified period.</p>

                    <div class="row">
                        <div class="col-sm-6">
                            <label for="">Payment
                                Period</label>
                            <input type="text" disabled placeholder="30 Days" class="disabled form-control">
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="payment_period_discount">Enter
                                    Discount
                                    Percentage</label>
                                <input type="text" name="payment_period_discount[thirty_days]" class="form-control">

                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <label>Payment
                                Period</label>
                            <input type="text" disabled placeholder="21 Days" class="disabled form-control">
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="payment_period_discount">Enter
                                    Discount
                                    Percentage</label>
                                <input type="text" name="payment_period_discount[twenty_one_days]"
                                    class="form-control">

                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <label>Payment
                                Period</label>
                            <input type="text" disabled placeholder="14 Days" class="disabled form-control">
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="payment_period_discount">Enter
                                    Discount
                                    Percentage</label>
                                <input type="text" name="payment_period_discount[fourteen_days]" class="form-control">

                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <label for="payment_period">Payment
                                Period</label>
                            <input type="text" disabled placeholder="7 Days" class="disabled form-control">
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="payment_period_discount">Enter
                                    Discount
                                    Percentage</label>
                                <input type="text" name="payment_period_discount[seventh_days]" class="form-control">

                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="payment_period">Payment Period</label>
                            <input type="text" disabled placeholder="3 Days" class="disabled form-control">
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="payment_period_discount">Enter
                                    Discount
                                    Percentage</label>
                                <input type="text" name="payment_period_discount[three_days]" class="form-control">

                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="payment_period">Advance/Upfront Payment</label>
                            <input type="text" disabled placeholder="Advance/Upfront Payment"
                                class="disabled form-control">
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="payment_period_discount">Enter
                                    Discount
                                    Percentage</label>
                                <input type="text" name="payment_period_discount[advance_upfront]"
                                    class="form-control">

                            </div>
                        </div>

                    </div>


                </div>
            </div>

            <div style="display:none" data-id="End month Discount" class="discount_form">
                <div>
                    <p>Invoices are routinely
                        discounted at the end of the month.</p>

                    <div class="">
                        <div class="form-group">
                            <label for="discount_value">Discount Value Type</label>

                            <select name="discount_value_type" class="discount_value_type form-control">
                                <option value="Percentage">Percentage</option>
                                <option value="Value">Value</option>
                            </select>

                        </div>
                        <div class="form-group show_hide_discount_value">
                            <label for="discount_value">Enter
                                Discount
                            </label>
                            <input type="text" name="discount_value" class="discount_value form-control">

                        </div>
                        <div class="form-group">
                            <label for="applies_to_all_item">
                                <input type="checkbox" name="applies_to_all_item" value="1"
                                    onchange="base_invoice_applies_to_all_item(this)" class="applies_to_all_item">
                                Discount
                                applies to all products</label>
                        </div>
                        <div class="form-group show_hide_select_products">
                            <div class="form-group">
                                <label for="">Select Product</label>
                                <select name="select-products" class="form-control select-products">
                                    <option value="" selected disabled>-- Select Product --</option>
                                    <option value="All">Select All</option>
                                    @foreach ($inventory as $key => $item)
                                        <option value='{{ $item->id }}' data-stock_id="{{ $item->stock_id_code }}">
                                            {{ $item->title }} {{ $item->net_weight }} KG</option>
                                    @endforeach
                                </select>
                            </div>
                            <table class="table table-bordered table-hover selected-product-list">
                                <thead>
                                    <tr>
                                        <th colspan="4">
                                            No. of Products: <span class="no_of_products_selected"></span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Discount</th>
                                        <th>Type</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            <div data-id="Quarterly Discount" style="display:none" class="discount_form">
                <div>
                    <p>Invoices are routinely
                        discounted quarterly.</p>

                    <div class="">
                        <div class="form-group ">
                            <label for="discount_value">Discount Value Type</label>

                            <select name="discount_value_type" class="discount_value_type form-control">
                                <option value="Percentage">Percentage</option>
                                <option value="Value">Value</option>
                            </select>

                        </div>
                        <div class="form-group show_hide_discount_value">
                            <label for="discount_value">Enter
                                Discount
                            </label>
                            <input type="text" name="discount_value" class="discount_value form-control">

                        </div>
                        <div class="form-group">
                            <label for="applies_to_all_item">
                                <input type="checkbox" name="applies_to_all_item" value="1"
                                    onchange="base_invoice_applies_to_all_item(this)" class="applies_to_all_item">
                                Discount
                                applies to all products</label>
                        </div>
                        <div class="form-group show_hide_select_products">
                            <div class="form-group">
                                <label for="">Select Product</label>
                                <select name="select-products" class="form-control select-products">
                                    <option value="" selected disabled>-- Select Product --</option>
                                    <option value="All">Select All</option>
                                    @foreach ($inventory as $key => $item)
                                        <option value='{{ $item->id }}' data-stock_id="{{ $item->stock_id_code }}">
                                            {{ $item->title }} {{ $item->net_weight }} KG</option>
                                    @endforeach
                                </select>
                            </div>
                            <table class="table table-bordered table-hover selected-product-list">
                                <thead>
                                    <tr>
                                        <th colspan="4">
                                            No. of Products: <span class="no_of_products_selected"></span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Discount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            <div data-id="Target discount on quantity" style="display:none" class="discount_form">
                <div>
                    <p>Products are routinely
                        discounted at the end of each month.</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="entered_target_quantity">Enter
                                    Target Quantity</label>
                                <input type="number" name="entered_target_quantity"
                                    class="entered_target_quantity form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="entered_discount_value">Enter
                                    Discount
                                    Percentage</label>
                                <input type="number" name="entered_discount_value"
                                    class="entered_discount_value form-control">
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="applies_to_all_item">
                            <input type="checkbox" name="applies_to_all_item" value="1"
                                onchange="target_quantity_applies_to_all_item(this)" class="applies_to_all_item">
                            Applies to all products</label>
                    </div>
                    <div class="">
                        <div class="form-group">
                            <label for="">Select Product</label>
                            <select name="select-products" class="form-control select-products">
                                <option value="" selected disabled>-- Select Product --</option>
                                <option value="All">Select All</option>
                                @foreach ($inventory as $key => $item)
                                    <option value='{{ $item->id }}' data-stock_id="{{ $item->stock_id_code }}">
                                        {{ $item->title }} {{ $item->net_weight }} KG</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="removeMe ">

                        <table class="table table-hover table-bordered selected-product-list">
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        No. of Products: <span class="no_of_products_selected"></span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Item</th>
                                    <th>Target Quantity</th>
                                    <th>Discount Value</th>
                                    <th>

                                    </th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                        {{-- <div class="col-sm-3">
                            <label for="target_quantity"
                                >Target
                                Quantity</label>
                            <input type="number" id="" name="target_quantity[]"
                                class="form-control">
                        </div>
    
                        <div class="col-sm-3">
                            <div class="">
                                <div class="form-group">
                                    <label for="target_discount"
                                        >Enter
                                        Discount
                                        Value</label>
                                    <input type="text" name="target_discount[]"
                                        class="form-control">
                                    
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-2">
                            <div>
                                <button type="button" class="btn btn-danger" onclick="$(this).parents('.removethisItem').remove()">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div> --}}


                    </div>

                </div>
            </div>

            <div data-id="Target discount on value" style="display:none" class="discount_form">
                <div>
                    <p>Products are routinely
                        discounted at the end of each month.</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="entered_purchased_quantity">Enter
                                    Purchase Quantity</label>
                                <input type="number" name="entered_purchased_quantity"
                                    class="entered_purchased_quantity form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="entered_value">Enter
                                    Value</label>
                                <input type="number" name="entered_value" class="entered_value form-control">
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="applies_to_all_item">
                            <input type="checkbox" name="applies_to_all_item" value="1"
                                onchange="target_value_applies_to_all_item(this)" class="applies_to_all_item">
                            Applies to all products</label>
                    </div>
                    <div class="form-group show_hide_select_products">
                        <div class="form-group">
                            <label for="">Select Product</label>
                            <select name="select-products" class="form-control select-products">
                                <option value="" selected disabled>-- Select Product --</option>
                                <option value="All">Select All</option>
                                @foreach ($inventory as $key => $item)
                                    <option value='{{ $item->id }}' data-stock_id="{{ $item->stock_id_code }}">
                                        {{ $item->title }} {{ $item->net_weight }} KG</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="table table-bordered table-hover selected-product-list">
                            <thead>
                                <tr>
                                    <th colspan="5">
                                        No. of Products: <span class="no_of_products_selected"></span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Purchase Quantity</th>
                                    <th>Value</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div data-id="Performance Discount" style="display:none" class="discount_form">
                <div>
                    <div class="form-group">

                        <table class="table table-bordered table-hover performance_discount_childs">
                            <thead>
                                <tr>
                                    <th>Slab</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Value</th>
                                    <th>
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="prepare_performance_discount_childs('','',''); return false;">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>



            <div data-id="Target discount on total value" style="display:none" class="discount_form">
                <div>
                    <p>Invoices are routinely
                        discounted at the end of each month.</p>

                    <div class="">
                        <div class="form-group">
                            <label for="discount_value">Discount Value Type</label>

                            <select name="discount_value_type" class="discount_value_type form-control">
                                <option value="Percentage">Percentage</option>
                                <option value="Value">Value</option>
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="discount_value">Enter
                                Discount
                            </label>
                            <input type="text" name="discount_value" class="discount_value form-control">
                        </div>
                        <div class="form-group">
                            <label for="max_discount">Enter
                                Value
                            </label>
                            <input type="text" name="max_discount" onkeyup="max_discount_change()"
                                class="max_discount form-control">
                        </div>
                        <div class="form-group">
                            <label for="target_type">Target</label>
                            <select name="target_type" class="target_type form-control" onchange="max_discount_change()">
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="discount_value_show" id="discount_value_show">Monthly</label>
                            <input type="text" readonly class="discount_value_show form-control" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <div data-id="Transport rebate per unit" style="display:none" class="discount_form">
                <div>
                    <p>Invoices are routinely
                        discounted at the end of each month.</p>

                    <div class="">
                        <h2>Discount Target</h2>
                    </div>

                    <div class="form-group">
                        <label for="discount_target_type">
                            <input type="checkbox" name="discount_target_type" value="All"
                                class="discount_target_type"
                                onchange="$('.transport_rebate_store_location_show_hide').toggle();">
                            Apply for
                            all locations</label>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group transport_rebate_store_location_show_hide">
                                <label for="transport_rebate_store_location">Select
                                    store location</label>
                                <select class="transport_rebate_store_location form-control">
                                    <option value="" selected disabled>-- Select Location --</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->location_name }} ({{ $location->location_code }})">
                                            {{ $location->location_name }} ({{ $location->location_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>




                    <div class="hide_this_on_location_select" style="display: none">
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <label for="">Discount</label>
                                    <input type="number" class="form-control transport_rebate_discount_for_all"
                                        name="" aria-describedby="helpId" placeholder="Enter Discount">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <br>
                                <button type="button" onclick="add_to_all_items_transport_rebate(this); return false;"
                                    class="btn btn-danger">Apply to All</button>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Per Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventory as $key => $item)
                                    <tr>
                                        <td scope="row" class="item_name">{{ $item->title }} {{ $item->net_weight }}
                                            KG</td>
                                        <td>
                                            <input type="number"
                                                class="form-control per_unit only_this per_unit_{{ $item->id }}"
                                                data-title="{{ $item->title }} {{ $item->net_weight }} KG"
                                                data-id="{{ $item->id }}" data-stock="{{ $item->stock_id_code }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" value="Application Stage" placeholder disabled
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Select Application Stage</label>

                                    <select name="application_stage[]" class="application_stage_discount form-control">
                                        <option value="" selected disabled>-- Select Application Stage --</option>
                                        <option value="Invoice Supplier">Invoice Supplier</option>
                                        <option value="Supplier Issues a Credit Note">Supplier Issues a Credit Note
                                        </option>
                                        <option value="Goods Equivalent to Transport Rebate Amount">Goods Equivalent to
                                            Transport Rebate Amount</option>
                                        <option value="Transport Rebate on Supplier Invoices">Transport Rebate on Supplier
                                            Invoices</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" onclick="add_transport_rebate_items('per_unit'); return;"
                                class="btn btn-primary">
                                Add </button>
                        </div>
                    </div>
                    <div class="add_transport_rebate_items"
                        style="
                max-height: 350px;
                overflow: auto;">

                    </div>
                </div>
            </div>
            <div data-id="Distribution Discount" style="display:none" class="discount_form">
                <div>
                    <p>Invoices are routinely
                        discounted at the end of each month.</p>

                    <div class="">
                        <h2>Discount Target</h2>
                    </div>

                    <div class="form-group">
                        <label for="discount_target_type">
                            <input type="checkbox" name="discount_target_type" value="All"
                                class="discount_target_type"
                                onchange="$('.transport_rebate_store_location_show_hide').toggle();">
                            Apply for
                            all locations</label>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group transport_rebate_store_location_show_hide">
                                <label for="transport_rebate_store_location">Select
                                    store location</label>
                                <select class="transport_rebate_store_location form-control">
                                    <option value="" selected disabled>-- Select Location --</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->location_name }} ({{ $location->location_code }})">
                                            {{ $location->location_name }} ({{ $location->location_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>




                    <div class="hide_this_on_location_select" style="display: none">
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <label for="">Discount</label>
                                    <input type="number" class="form-control transport_rebate_discount_for_all"
                                        name="" aria-describedby="helpId" placeholder="Enter Discount">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <br>
                                <button type="button" onclick="add_to_all_items_transport_rebate(this); return false;"
                                    class="btn btn-danger">Apply to All</button>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Per Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventory as $key => $item)
                                    <tr>
                                        <td scope="row" class="item_name">{{ $item->title }} {{ $item->net_weight }}
                                            KG</td>
                                        <td>
                                            <input type="number"
                                                class="form-control per_unit only_this per_unit_{{ $item->id }}"
                                                data-title="{{ $item->title }} {{ $item->net_weight }} KG"
                                                data-id="{{ $item->id }}" data-stock="{{ $item->stock_id_code }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" value="Application Stage" placeholder disabled
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Select Application Stage</label>

                                    <select name="application_stage[]" class="application_stage_discount form-control">
                                        <option value="" selected disabled>-- Select Application Stage --</option>
                                        <option value="Invoice Supplier">Invoice Supplier</option>
                                        <option value="Supplier Issues a Credit Note">Supplier Issues a Credit Note
                                        </option>
                                        <option value="Goods Equivalent to Transport Rebate Amount">Goods Equivalent to
                                            Transport Rebate Amount</option>
                                        <option value="Transport Rebate on Supplier Invoices">Transport Rebate on Supplier
                                            Invoices</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" onclick="add_transport_rebate_items('per_unit'); return;"
                                class="btn btn-primary">
                                Add </button>
                        </div>
                    </div>
                    <div class="add_transport_rebate_items"
                        style="
                            max-height: 350px;
                            overflow: auto;
                        ">

                    </div>
                </div>
            </div>
            <div data-id="Distribution Discount on Delivery" style="display:none" class="discount_form">
                <div>
                    <p>Invoices are routinely
                        discounted at the end of each month.</p>

                    <div class="">
                        <h2>Discount Target</h2>
                    </div>

                    <div class="form-group">
                        <label for="discount_target_type">
                            <input type="checkbox" name="discount_target_type" value="All"
                                class="discount_target_type"
                                onchange="$('.transport_rebate_store_location_show_hide').toggle();">
                            Apply for
                            all locations</label>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group transport_rebate_store_location_show_hide">
                                <label for="transport_rebate_store_location">Select
                                    store location</label>
                                <select class="transport_rebate_store_location form-control">
                                    <option value="" selected disabled>-- Select Location --</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->location_name }} ({{ $location->location_code }})">
                                            {{ $location->location_name }} ({{ $location->location_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>




                    <div class="hide_this_on_location_select" style="display: none">
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <label for="">Discount</label>
                                    <input type="number" class="form-control transport_rebate_discount_for_all"
                                        name="" aria-describedby="helpId" placeholder="Enter Discount">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <br>
                                <button type="button" onclick="add_to_all_items_transport_rebate(this); return false;"
                                    class="btn btn-danger">Apply to All</button>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Per Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventory as $key => $item)
                                    <tr>
                                        <td scope="row" class="item_name">{{ $item->title }} {{ $item->net_weight }}
                                            KG</td>
                                        <td>
                                            <input type="number"
                                                class="form-control per_unit only_this per_unit_{{ $item->id }}"
                                                data-title="{{ $item->title }} {{ $item->net_weight }} KG"
                                                data-id="{{ $item->id }}" data-stock="{{ $item->stock_id_code }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" value="Application Stage" placeholder disabled
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Select Application Stage</label>

                                    <select name="application_stage[]" class="application_stage_discount form-control">
                                        <option value="" selected disabled>-- Select Application Stage --</option>
                                        <option value="Invoice Supplier">Invoice Supplier</option>
                                        <option value="Supplier Issues a Credit Note">Supplier Issues a Credit Note
                                        </option>
                                        <option value="Goods Equivalent to Transport Rebate Amount">Goods Equivalent to
                                            Transport Rebate Amount</option>
                                        <option value="Transport Rebate on Supplier Invoices">Transport Rebate on Supplier
                                            Invoices</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" onclick="add_transport_rebate_items('per_unit'); return;"
                                class="btn btn-primary">
                                Add </button>
                        </div>
                    </div>
                    <div class="add_transport_rebate_items"
                        style="
                            max-height: 350px;
                            overflow: auto;
                        ">

                    </div>
                </div>
            </div>
            <div data-id="Transport rebate percentage" style="display:none" class="discount_form">
                <div>
                    <p>Invoices are routinely
                        discounted at the end of each month.</p>

                    <div class="">
                        <h2>Discount Target</h2>
                    </div>

                    <div class="form-group">
                        <label for="discount_target_type">
                            <input type="checkbox" name="discount_target_type" value="All"
                                class="discount_target_type"
                                onchange="$('.transport_rebate_store_location_show_hide').toggle(); ">
                            Apply for
                            all locations</label>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group transport_rebate_store_location_show_hide">
                                <label for="transport_rebate_store_location">Select
                                    store location</label>
                                <select class="transport_rebate_store_location form-control">
                                    <option value="" selected disabled>-- Select Location --</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->location_name }} ({{ $location->location_code }})">
                                            {{ $location->location_name }} ({{ $location->location_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>




                    <div class="hide_this_on_location_select" style="display: none">
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <label for="">Discount</label>
                                    <input type="number" class="form-control transport_rebate_discount_for_all"
                                        name="" aria-describedby="helpId" placeholder="Enter Discount">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <br>
                                <button type="button" onclick="add_to_all_items_transport_rebate(this); return false;"
                                    class="btn btn-danger">Apply to All</button>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>% of Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventory as $key => $item)
                                    <tr>
                                        <td scope="row">{{ $item->title }} {{ $item->net_weight }} KG</td>
                                        <td>
                                            <input type="number"
                                                class="form-control percentage_of_invoice only_this percentage_of_invoice_{{ $item->id }}"
                                                data-title="{{ $item->title }} {{ $item->net_weight }} KG"
                                                data-id="{{ $item->id }}" data-stock="{{ $item->stock_id_code }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" value="Application Stage" placeholder disabled
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Select Application Stage</label>

                                    <select name="application_stage[]" class="application_stage_discount form-control">
                                        <option value="" selected disabled>-- Select Application Stage --</option>
                                        <option value="Invoice Supplier">Invoice Supplier</option>
                                        <option value="Supplier Issues a Credit Note">Supplier Issues a Credit Note
                                        </option>
                                        <option value="Goods Equivalent to Transport Rebate Amount">Goods Equivalent to
                                            Transport Rebate Amount</option>
                                        <option value="Transport Rebate on Supplier Invoices">Transport Rebate on Supplier
                                            Invoices</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" onclick="add_transport_rebate_items('percentage_of_invoice'); return;"
                                class="btn btn-primary">
                                Add </button>
                        </div>

                    </div>
                    <div class="add_transport_rebate_items"
                        style="
                max-height: 350px;
                overflow: auto;">

                    </div>
                </div>
            </div>
            <div data-id="Transport rebate per tonnage" style="display:none" class="discount_form">
                <div>
                    <p>Invoices are routinely
                        discounted at the end of each month.</p>

                    <div class="">
                        <h2>Discount Target</h2>
                    </div>

                    <div class="form-group">
                        <label for="discount_target_type">
                            <input type="checkbox" name="discount_target_type" value="All"
                                class="discount_target_type"
                                onchange="$('.transport_rebate_store_location_show_hide').toggle()">
                            Apply for
                            all locations</label>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group transport_rebate_store_location_show_hide">
                                <label for="transport_rebate_store_location">Select
                                    store location</label>
                                <select class="transport_rebate_store_location form-control">
                                    <option value="" selected disabled>-- Select Location --</option>
                                    @foreach ($locations as $location)
                                        <option
                                            value="{{ $location->location_name }} ({{ $location->location_code }})">
                                            {{ $location->location_name }} ({{ $location->location_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>




                    <div class="hide_this_on_location_select" style="display: none">
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <label for="">Discount</label>
                                    <input type="number" class="form-control transport_rebate_discount_for_all"
                                        name="" aria-describedby="helpId" placeholder="Enter Discount">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <br>
                                <button type="button" onclick="add_to_all_items_transport_rebate(this); return false;"
                                    class="btn btn-danger">Apply to All</button>
                            </div>
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Per Tonnage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventory as $key => $item)
                                    <tr>
                                        <td scope="row">{{ $item->title }} {{ $item->net_weight }} KG</td>
                                        <td>
                                            <input type="number"
                                                class="form-control per_tonnage per_tonnage_{{ $item->id }} only_this"
                                                data-title="{{ $item->title }} {{ $item->net_weight }} KG"
                                                data-id="{{ $item->id }}"
                                                data-stock="{{ $item->stock_id_code }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" value="Application Stage" placeholder disabled
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Select Application Stage</label>

                                    <select name="application_stage[]" class="application_stage_discount form-control">
                                        <option value="" selected disabled>-- Select Application Stage --</option>
                                        <option value="Invoice Supplier">Invoice Supplier</option>
                                        <option value="Supplier Issues a Credit Note">Supplier Issues a Credit Note
                                        </option>
                                        <option value="Goods Equivalent to Transport Rebate Amount">Goods Equivalent to
                                            Transport Rebate Amount</option>
                                        <option value="Transport Rebate on Supplier Invoices">Transport Rebate on Supplier
                                            Invoices</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" onclick="add_transport_rebate_items('per_tonnage'); return;"
                                class="btn btn-primary">
                                Add </button>
                        </div>

                    </div>
                    <div class="add_transport_rebate_items"
                        style="
                                max-height: 350px;
                                overflow: auto;">

                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title delete-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                </div>
                <div class="modal-body">
                    <p class="delete-p">
                        Are you sure you want to remove this item?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn"> <i class="fa fa-trash"></i>
                        &nbsp; Remove</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="" method="post" class="submitMe">
            @csrf
            @method('PUT')
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $(document).ready(function() {

            var formmessage = new Form()

            var table = $("#productsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('trade-agreement.edit', $trade->id) !!}',
                    data: function(data) {}
                },
                columns: [{
                        data: "stock_id_code",
                        name: "wa_inventory_items.stock_id_code"
                    }, {
                        data: "title",
                        name: "wa_inventory_items.title"
                    }, {
                        data: "pack_size",
                        name: "pack_sizes.title"
                    }, {
                        data: "price_list_cost",
                        name: "wa_inventory_items.price_list_cost"
                    }, {
                        data: "base_discount",
                        name: "base_discount"
                    }, {
                        data: "invoice_discount",
                        name: "invoice_discount"
                    }, {
                        data: "standard_cost",
                        name: "wa_inventory_items.standard_cost"
                    },
                    {
                        data: "selling_price",
                        name: "wa_inventory_items.selling_price",
                        render: function(data, type, row) {
                            return data ? data : '0.00';
                        }
                    },
                    {
                        data: "margin_type",
                        name: "wa_inventory_items.margin_type",
                        render: function(data, type, row) {
                            return data == 0 ? 'Value' : 'Percentage';
                        }
                    },
                    {
                        data: "percentage_margin",
                        name: "wa_inventory_items.percentage_margin",
                        render: function(data, type, row) {
                            return data ? data : '0.00';
                        }
                    },
                    {
                        data: "offer_date",
                        name: "trade_product_offers.created_at"
                    },
                    {
                        data: null,
                        name: "actions",
                        render: function(data, type, row) {
                            return `
                            <a href="#" 
                       data-id="${row.id}" 
                       data-title="${row.title}" 
                       data-stock-code="${row.stock_id_code}" 
                       data-price-list-cost="${row.price_list_cost}"
                       class="edit-link" 
                       title="Edit">
                        <i class="fa fa-pencil"></i> &nbsp;
                    </a>
                    
                <a href="#" 
                   data-id="${row.id}" 
                   data-title="${row.title}" 
                   data-stock-code="${row.stock_id_code}" 
                   class="retire-link" 
                   title="Retire">
                    <i class="fa fa-times"></i>
                </a>`;
                        },
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    }

                ]
            });

            $(document).on('click', '#open_update_modal_btn', function() {

                var data = table.rows().data().toArray();

                var tbody = $('#updateItemsTable tbody');
                tbody.empty();

                data.forEach(function(item) {
                    tbody.append(`
                <tr>
                    <td>${item.stock_id_code}</td>
                    <td>${item.title}</td>
                    <td>
                        <input type="text" name="price_list_cost[]" value="${item.price_list_cost}" 
                               data-id="${item.id}" class="form-control price-list-cost-input">
                    </td>
                </tr> `);
                });

                $('#openUpdateItemsModal').modal('show');
            });

            $(document).on('click', '.edit-link', function(e) {
                e.preventDefault();

                var $this = $(this);
                var id = $this.data('id');
                var title = $this.data('title');
                var stockCode = $this.data('stock-code');
                var priceListCost = $this.data('price-list-cost');

                var tbody = $('#updateItemsTable tbody');
                tbody.empty();
                tbody.append(`
                    <tr>
                        <td>${stockCode}</td>
                        <td>${title}</td>
                        <td>
                            <input type="text" name="price_list_cost[]" value="${priceListCost}" 
                                data-id="${id}" class="form-control price-list-cost-input">
                        </td>
                    </tr>
                `);

                $('#openUpdateItemsModal').modal('show');
            });


            $(document).on('click', '#update-item-cost', function() {
                var submitButton = $('#update-item-cost');

                submitButton.html('<i class="fa fa-spinner fa-spin"></i> Processing....');
                submitButton.prop('disabled', true);

                var data = [];

                $('#updateItemsTable tbody tr').each(function() {
                    var item_id = $(this).find('.price-list-cost-input').data('id');
                    var price_list_cost = $(this).find('.price-list-cost-input').val();

                    data.push({
                        item_id,
                        price_list_cost
                    });
                });

                $.ajax({
                    url: '{!! route('update_trade_agreement.item_cost') !!}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        items: data
                    },
                    success: function(response) {
                        $('#openUpdateItemsModal').modal('hide');
                        table.ajax.reload();
                        formmessage.successMessage('Costs Updated')
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'Something went wrong.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        submitButton.html('<i class="fa fa-upload"></i> Update');
                        submitButton.prop('disabled', false);
                    }
                });
            });
        });

        function openTheLockModal(url, status, trade) {
            $("#modelId form").attr('action', url);
            $('#modelId .modal-title').html(status + " Trade Agreement");
            $('#modelId .modal-body').html("Click confirm to " + status + " this trade agreement: " + trade);
            $('#modelId').modal('show');
        }
    </script>
@endpush

@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        .loader {
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before,
        #loader-1:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 100%;
            border: 10px solid transparent;
            border-top-color: #3498db;
        }

        #loader-1:before {
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after {
            border: 10px solid #ccc;
        }

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .badge-bizwiz {
            background-color: #0086ff21;
            color: black;
            font-weight: 500;
            padding: 5px 12px;
            border: 1px solid #0074ff;
            margin: 2px;
        }

        .badge-bizwiz a {
            margin-left: 4px;
            font-size: 16px;
            font-weight: 900;
        }

        .delete-title {
            color: red;
            font-weight: bolder;
            font-size: 20px;
        }

        .update-title {
            color: black;
            font-weight: bolder;
            font-size: 20px;
        }

        p.delete-p {
            font-size: 17px;
        }
    </style>
    <div id="loader-on"
        style="position: absolute;top: 0;text-align: center;display: block;z-index: 999999;width: 100%;height: 100%;background: #000000b8;display:none;"
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>

    <script src="{{ asset('js/sweetalert.js') }}"></script>
    {{-- <script src="{{ asset('js/form.js') }}"></script> --}}
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $('.open-select2').select2();

        $(document).ready(function() {

            var messageform = new Form()

            $(document).on('click', '.retire-link', function(e) {
                e.preventDefault();

                var itemId = $(this).data('id');
                var itemTitle = $(this).data('title');
                var stockCode = $(this).data('stock-code');
                var uom = $(this).data('uom');
                var price = $(this).data('price');
                var modal = $('#retireItemModal');

                var formAction = '{{ route('trade_agreement_item.retire', ':id') }}';
                formAction = formAction.replace(':id', itemId);
                modal.find('form').attr('action', formAction);

                modal.find('.modal-body').html(`
                    <div class="form-group">
                        <label>Are you sure you want to retire the following item?</label>
                        <p><strong>Item Code:</strong> ${stockCode}</p>
                        <p><strong>Item Name:</strong> ${itemTitle}</p>
                    </div>
                `);

                modal.modal('show');
            });


            $(document).on('click', '#retire-item-btn', function(e) {
                e.preventDefault();

                var submitButton = $(this);
                var form = submitButton.closest('form');
                var actionUrl = form.attr('action');

                submitButton.html('<i class="fa fa-spinner fa-spin"></i> Processing....');
                submitButton.prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: actionUrl,
                    data: form.serialize(),
                    success: function(response) {
                        messageform.successMessage('Item retired successfully.')
                        $('#retireItemModal').modal('hide');
                        setTimeout(() => {
                            location.reload()
                        }, 2000);
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'Something went wrong.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        submitButton.html('<i class="fa fa-times"></i> Retire');
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.remove-btn', function() {
                var row = $(this).closest('tr');

                $('#confirmDeleteModal').modal('show');

                $('#confirmDeleteBtn').off('click').on('click', function() {
                    row.remove();
                    $('#confirmDeleteModal').modal('hide');
                });
            });


            $('#download-trade-agreement-items').on('submit', function(e) {
                e.preventDefault();

                var submitButton = $(this).find('button[type="submit"]:focus');
                var intentValue = submitButton.val();

                submitButton.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentValue);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = (intentValue === 'Download Pdf' || intentValue === 'Download Excel') ?
                    'blob' : 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (intentValue === 'Download Pdf' || intentValue === 'Download Excel') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = intentValue === 'Download Pdf' ?
                                'TRADE-AGREEMENT-ITEMS.pdf' :
                                'TRADE-AGREEMENT-ITEMS.xlsx';
                            link.click();
                            form.successMessage('File downloaded successfully.')
                        } else {
                            form.successMessage('File downloaded successfully.')
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        var errorMessage = 'Something went wrong.';
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response && response.error) {
                                errorMessage = response.error;
                            }
                        } catch (err) {}
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    submitButton.prop('disabled', false).html(submitButton.data('original-text'));
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.',
                    });
                    submitButton.prop('disabled', false).html(submitButton.data('original-text'));
                };

                xhr.send(formData);
            });

            $('#download-trade-agreement-items button[type="submit"]').each(function() {
                $(this).data('original-text', $(this).html());
            });


        });


        $('.inventory_item_select').change(function(e) {
            e.preventDefault();
            let stock = $(this).find(`option[value="${$(this).val()}"]`).data('stock_id');
            console.log(stock);
            $(this).parents('.form-group').find('input[name="stock_id_code"]').val(stock);
            console.log($(this).parents('.form-group').find('input[name="stock_id_code"]').val());
        })
        var selected_product_list = [];
        var selected_product_list_purchase_quantity = [];
        var transport_rebate_store_locations = [];
        var target_discount_childs = [];

        function openAddProductOfferModal(stock_id_code, id, name, offer_amount, target_quantity) {
            $('.loder').css('display', 'block');
            $(document).find('.addProductOfferModal .product_name').val(name);
            $(document).find('.addProductOfferModal input[name="stock_id_code"]').val(stock_id_code);
            $(document).find('.addProductOfferModal input[name="inventory_item_id"]').val(id);
            $(document).find('.addProductOfferModal .offer_amount').val(offer_amount);
            $(document).find('.addProductOfferModal .target_quantity').val(target_quantity);

            $('#openProductOfferModal').modal('show')
            $('.loder').css('display', 'none');
        }

        function addMoreRows() {
            row = ` <tr>
                <td><input type="text" class="form-control" value="" name="summary[]"></td>
                <td>
                    <a href="#" class="remove-btn">
                        <i class="fa fa-trash"  style="margin-top: 10px"></i>
                    </a>
                </td>
            </tr>`;
            $('#add_summary tbody').append(row);
        }

        function show_discount_form(input) {
            selectedDataId = $(input).val();
            $('#add_form_here').html("");
            let design = $(document).find("div.discount_form[data-id='" + selectedDataId + "']").html();
            $('#add_form_here').html(design);
            $('#add_form_here').find('.select-products').select2();
        }

        $(document).on('keyup',
            '.show_hide_discount_value input,.entered_target_quantity,.entered_discount_value,.entered_purchased_quantity,.entered_value',
            function(e) {
                $(document).find('.submitPerform .applies_to_all_item').change();
            });

        function target_value_applies_to_all_item(input) {
            if ($(input).is(':checked')) {
                entered_value = parseFloat($(document).find('.submitPerform .entered_value').val());
                entered_purchased_quantity = parseFloat($(document).find('.submitPerform .entered_purchased_quantity')
                    .val());
                if (entered_purchased_quantity > 0) {
                    $(document).find('.selected_product_quantity').each(function(indexInArray, valueOfElement) {
                        $(this).val(entered_purchased_quantity);
                    })
                }
                if (entered_value > 0) {
                    $(document).find('.selected_product_offer').each(function(indexInArray, valueOfElement) {
                        $(this).val(entered_value);
                    })
                }
            }
        }

        function base_invoice_applies_to_all_item(input) {
            if ($(input).is(':checked')) {
                discount = parseFloat($(document).find('.submitPerform .show_hide_discount_value input').val());
                if (discount > 0) {
                    $(document).find('.selected_product_discount').each(function(indexInArray, valueOfElement) {
                        $(this).val(discount);
                    })
                }
            }
        }

        function target_quantity_applies_to_all_item(input) {
            if ($(input).is(':checked')) {
                entered_target_quantity = parseFloat($(document).find('.submitPerform .entered_target_quantity').val());
                entered_discount_value = parseFloat($(document).find('.submitPerform .entered_discount_value').val());
                if (entered_discount_value > 0) {
                    $(document).find('.target_discount').each(function(indexInArray, valueOfElement) {
                        $(this).val(entered_discount_value);
                    })
                }
                if (entered_target_quantity > 0) {
                    $(document).find('.target_quantity').each(function(indexInArray, valueOfElement) {
                        $(this).val(entered_target_quantity);
                    })
                }
            }
        }

        function removeTrasnportRebateItem(input) {
            let location = $(input).parents('.removethisitem').find('.store_location').val();
            if (!transport_rebate_store_locations.includes(location)) {
                form.errorMessage('No such location exists!');
                return;
            }
            let index = transport_rebate_store_locations.indexOf(location);
            if (index !== -1) {
                transport_rebate_store_locations.splice(index, 1);
            }
            $(input).parents('.removethisitem').remove();
            return;
        }

        function opendiscountmodel(id) {
            $.ajax({
                type: "GET",
                url: "{{ route('trade-agreement.get_discount', $trade->id) }}",
                data: {
                    'discount_id': id
                },
                success: function(response) {
                    if (response.result == 1) {
                        switch (response.data.discount_type) {
                            case "Base Discount":
                                openBaseDiscount(response.data);
                                break;
                            case "Bank Guarantee Discount":
                                openBaseDiscount(response.data);
                                break;
                            case "Invoice Discount":
                                openBaseDiscount(response.data);
                                break;
                            case "End month Discount":
                                openBaseDiscount(response.data);
                                break;
                            case "Quarterly Discount":
                                openBaseDiscount(response.data);
                                break;
                            case "Target discount on value":
                                openTargetPurhcaseDiscount(response.data);
                                break;
                            case "Purchase Quantity Offer":
                                openTargetPurhcaseDiscount(response.data);
                                break;
                            case "Payment Discount":
                                openPaymentDiscount(response.data);
                                break;
                            case "Target discount on quantity":
                                openTargetQuantityDiscount(response.data);
                                break;
                            case "Transport rebate per unit":
                                openTrasnportRebateDiscount(response.data);
                                break;
                            case "Distribution Discount":
                                openTrasnportRebateDiscount(response.data);
                                break;
                            case "Distribution Discount on Delivery":
                                openTrasnportRebateDiscount(response.data);
                                break;
                            case "Transport rebate percentage":
                                openTrasnportRebateDiscount(response.data);
                                break;
                            case "Transport rebate per tonnage":
                                openTrasnportRebateDiscount(response.data);
                                break;
                            case "Performance Discount":
                                openPerformanceDiscount(response.data);
                                break;
                            default:
                                openDiscountValue(response.data);
                                break;
                        }
                    }
                }
            });
        }

        function formatCommaSeparated(input) {
            // Remove any existing commas and trim any whitespace
            let value = input.val().replace(/,/g, '').trim();
            // If the value is not empty, format it as comma-separated
            if (value) {
                input.parent().find('.real_value_container').val(value);
                input.val(Number(value).toLocaleString());
                console.log(input.val(), value);
            }
        }

        function makeMeCommaSeperated(input) {
            let new_in = $(input);
            // Clear any previous timeout to reset the timer
            clearTimeout(new_in.data('timeout'));
            // Set a timeout to format the value after typing stops (e.g., 500ms)
            new_in.data('timeout', setTimeout(() => {
                formatCommaSeparated(new_in);
            }, 500)); // 500ms delay after the user stops typing
        }

        function prepare_performance_discount_childs(from, to, value) {
            $('#add_form_here .performance_discount_childs tbody').append(`
                <tr>
                    <td>
                        <input type="text" readonly name="slab[]" value="Slab" class="form-control">
                    </td>
                    
                    <td>
                        <input type="text" value="${from}" class="form-control makeMeCommaSeperated" onchange="makeMeCommaSeperated(this)">
                        <input type="hidden" name="from[]" value="${from}"  class="form-control real_value_container">
                    </td>
                    <td>
                        <input type="text" value="${to}"  class="form-control makeMeCommaSeperated" onchange="makeMeCommaSeperated(this)">
                        <input type="hidden" name="to[]" value="${to}"  class="form-control real_value_container">
                    </td>
                    <td>
                        <input type="number" id="" name="value[]" value="${value}"  class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger" onclick="$(this).parents('tr').remove(); perform_slab_number(); return false;"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `);
            perform_slab_number();
        }

        function perform_slab_number() {
            let performance_slabs = 0;
            $(document).find('input[name="slab[]"]').each(function(indexInArray, valueOfElement) {
                performance_slabs += 1;
                $(this).val('Slab ' + performance_slabs);
            });
            $('.makeMeCommaSeperated').change();
        }

        function openPerformanceDiscount(obj) {
            $('.submitPerform #discount_type').val(obj.discount_type).change();
            performance_slabs = [];
            let other_options = JSON.parse(obj.other_options);
            $.each(other_options, function(indexInArray, valueOfElement) {
                prepare_performance_discount_childs(valueOfElement.from, valueOfElement.to, valueOfElement.value);
            });
            $('.close_discount_modal').click();
        }

        function openBaseDiscount(obj) {
            $('.submitPerform #discount_type').val(obj.discount_type).change();
            $(document).find('.submitPerform .discount_value_type').val(obj.discount_value_type);
            $(document).find('.submitPerform .applies_to_all_item').prop('checked', obj.applies_to_all_item ? true : false)
                .change();
            let other_options = JSON.parse(obj.other_options);
            console.log(selected_product_list);
            selected_product_list.splice(0, selected_product_list.length);
            $.each(other_options, function(indexInArray, valueOfElement) {
                $(document).find('#add_form_here select.select-products').val(indexInArray).change();
                $(document).find(`#add_form_here [name="selected_product_discount[${indexInArray}]"`).val(
                    valueOfElement.discount);
                if (valueOfElement.type) {
                    $(document).find(`#add_form_here [name="selected_product_discount_type[${indexInArray}]"`).val(
                        valueOfElement.type);
                }
            });
            $('.close_discount_modal').click();
        }

        function add_target_discount_childs(val, stock_id, title, is_all = false) {
            if (!target_discount_childs.includes(val)) {
                target_discount_childs.push(val);
                let chec = $(document).find('.submitPerform .applies_to_all_item').is(":checked");

                entered_target_quantity = chec ? parseFloat($(document).find('.submitPerform .entered_target_quantity')
                    .val()) : null;
                entered_discount_value = chec ? parseFloat($(document).find('.submitPerform .entered_discount_value')
                    .val()) : null;
                $(document).find('#add_form_here .selected-product-list tbody').prepend(`
                    <tr>
                        <td>
                            <input type="hidden" name="selected_products[${val}]" data-id="${val}" value="${stock_id}">
                            ${stock_id} ${title}
                        </td>
                        
                        <td>
                            <input type="number" id="" name="target_quantity[${val}]" value="${entered_target_quantity}" class="form-control target_quantity">
                        </td>
                        <td>
                            <input type="number" id="" name="target_discount[${val}]" value="${entered_discount_value}" class="form-control target_discount">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger delete_from_list" data-id="${val}"><i class="fa fa-trash"></i></button>
                            </td>
                    </tr>
                `);
            } else {
                if (!is_all) {
                    form.errorMessage(title + " : already selected");
                }
            }
        }

        function add_base_discount_childs(val, stock_id, title, discount_type, is_all = false) {
            if (!selected_product_list.includes(val)) {
                let chec = $(document).find('.submitPerform .applies_to_all_item').is(":checked");

                discount = chec ? parseFloat($(document).find('.submitPerform .show_hide_discount_value input').val()) :
                    null;
                selected_product_list.push(val);
                d_type = "";
                if (discount_type == "End month Discount" || discount_type == "Base Discount" || discount_type ==
                    "Bank Guarantee Discount") {
                    d_type = `<td>
                        <select name="selected_product_discount_type[${val}]" class="selected_product_discount_type form-control">
                            <option value="Percentage">Percentage</option>
                            <option value="Value">Value</option>
                        </select>
                    </td>`;
                }
                $(document).find('#add_form_here .selected-product-list tbody').prepend(`
                    <tr>
                        <td>
                            <input type="hidden" name="selected_products[${val}]" data-id="${val}" value="${stock_id}">
                            ${stock_id}
                        </td>
                        <td>
                            ${title}
                        </td>
                        <td>
                            <input type="text" id="" name="selected_product_discount[${val}]" value="${discount}" class="form-control selected_product_discount">
                        </td>
                        ${d_type}
                        <td>
                            <button type="button" class="btn btn-danger delete_from_list" data-id="${val}"><i class="fa fa-trash"></i></button>
                            </td>
                    </tr>
                `);
            } else {
                if (!is_all) {
                    form.errorMessage(title + " : already selected");
                }
            }
        }

        function add_selected_product_list_purchase_quantity(val, stock_id, title, is_all = false) {
            if (!selected_product_list_purchase_quantity.includes(val)) {
                selected_product_list_purchase_quantity.push(val);
                let chec = $(document).find('.submitPerform .applies_to_all_item').is(":checked");
                entered_value = chec ? parseFloat($(document).find('.submitPerform .entered_value').val()) : null;
                entered_purchased_quantity = chec ? parseFloat($(document).find(
                    '.submitPerform .entered_purchased_quantity').val()) : null;
                $(document).find('#add_form_here .selected-product-list tbody').prepend(`
                    <tr>
                        <td>
                            <input type="hidden" name="selected_products[${val}]" data-id="${val}" value="${stock_id}">
                            ${stock_id}
                        </td>
                        <td>
                            ${title}
                        </td>
                        <td>
                            <input type="number" name="selected_product_quantity[${val}]" value="${entered_purchased_quantity}" class="form-control selected_product_quantity">
                        </td>
                        <td>
                            <input type="number" name="selected_product_offer[${val}]" value="${entered_value}" class="form-control selected_product_offer">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger delete_from_list" data-id="${val}"><i class="fa fa-trash"></i></button>
                            </td>
                    </tr>
                `);
            } else {
                if (!is_all) {
                    form.errorMessage(title + " : already selected");
                }
            }
        }

        $(document).on('change', '#add_form_here select.select-products', function(e) {
            e.preventDefault();
            let val = $(this).val();
            let id = $(this).parents('#discount_settings').find('.discount_type').val();
            console.log(target_discount_childs);
            if (val == 'All') {
                @foreach ($inventory as $key => $item)
                    if (id == "Target discount on quantity") {
                        add_target_discount_childs('{{ $item->id }}', '{{ $item->stock_id_code }}',
                            '{{ $item->title }}', true);
                    }
                    if (id == "Base Discount" || id == "Invoice Discount" || id == "End month Discount" || id ==
                        "Quarterly Discount" || id == 'Bank Guarantee Discount') {
                        add_base_discount_childs('{{ $item->id }}', '{{ $item->stock_id_code }}',
                            '{{ $item->title }}', id, true);
                    }
                    if (id == 'Purchase Quantity Offer' || id == "Target discount on value") {
                        add_selected_product_list_purchase_quantity('{{ $item->id }}',
                            '{{ $item->stock_id_code }}', '{{ $item->title }}', true);
                    }
                @endforeach
                return;
            }

            let option = $(this).find(`option[value="${val}"]`)
            let stock_id = option.data('stock_id');
            let title = option.html();
            if (id == "Base Discount" || id == "Invoice Discount" || id == "End month Discount" || id ==
                "Quarterly Discount" || id == 'Bank Guarantee Discount') {
                add_base_discount_childs(val, stock_id, title, id);
            }
            if (id == "Target discount on quantity") {
                add_target_discount_childs(val, stock_id, title);
            }
            if (id == 'Purchase Quantity Offer' || id == "Target discount on value") {
                add_selected_product_list_purchase_quantity(val, stock_id, title);
            }
            let count = $(document).find("#add_form_here tbody tr").length;
            $(document).find("#add_form_here .no_of_products_selected").html(count);
        });
        $(document).on('click', '#add_form_here .delete_from_list', function(e) {
            e.preventDefault();
            let id = $(this).parents('#discount_settings').find('.discount_type').val();
            let val = $(this).data('id');
            if (id == "Base Discount" || id == 'Bank Guarantee Discount') {
                var index = selected_product_list.indexOf(`${val}`);
                if (index !== -1) {
                    selected_product_list.splice(index, 1);
                }
            }
            if (id == 'Purchase Quantity Offer') {
                var index = selected_product_list_purchase_quantity.indexOf(`${val}`);
                if (index !== -1) {
                    selected_product_list_purchase_quantity.splice(index, 1);
                }
            }
            $(this).parents('tr').remove();
        });

        function openDiscountValue(obj) {
            $('.submitPerform #discount_type').val(obj.discount_type).change();

            $(document).find('.submitPerform .discount_value_type').val(obj.discount_value_type);
            $(document).find('.submitPerform .discount_value').val(obj.discount_value);
            if (obj.discount_type == 'Target discount on total value') {
                let other_options = JSON.parse(obj.other_options);
                let target_type = other_options.target_type;
                $(document).find('.submitPerform .target_type').val(target_type).change();
                $(document).find('.submitPerform .max_discount').val(other_options.max_discount);
                max_discount_change();
            }
            $('.close_discount_modal').click();
        }

        function max_discount_change() {
            let target_type = $(document).find('.submitPerform .target_type').val()
            let max_discount = $(document).find('.submitPerform .max_discount').val();
            $(document).find('.submitPerform #discount_value_show').html(target_type);
            if (target_type == "Quarterly") {
                $(document).find('.submitPerform .discount_value_show').val(parseFloat(max_discount) * 3);
            } else {
                $(document).find('.submitPerform .discount_value_show').val(max_discount);
            }
        }

        function openTargetDiscount(obj) {
            $('.submitPerform #discount_type').val(obj.discount_type).change();
            $(document).find('.submitPerform .applies_to_all_item').prop('checked', obj.applies_to_all_item ? true : false)
                .change();
            $(document).find('.submitPerform .discount_value').val(obj.discount_value);
            $(document).find('.submitPerform .purchased_product_quantity').val(obj.purchased_product_quantity);
            $(document).find('.submitPerform .free_product_quantity').val(obj.free_product_quantity);

            $('.close_discount_modal').click();
        }

        function openTargetPurhcaseDiscount(obj) {
            $('.submitPerform #discount_type').val(obj.discount_type).change();
            let other_options = JSON.parse(obj.other_options);
            selected_product_list_purchase_quantity = [];
            $.each(other_options, function(indexInArray, valueOfElement) {
                if (valueOfElement.purchase_quantity > 0) {
                    $(document).find('#add_form_here select.select-products').val(indexInArray).change();
                    $(document).find(
                        `.selected_product_quantity[name="selected_product_quantity[${indexInArray}]"]`).val(
                        valueOfElement.purchase_quantity)
                    $(document).find(`.selected_product_offer[name="selected_product_offer[${indexInArray}]"]`).val(
                        valueOfElement.free_stock)
                }
            });
            $('.close_discount_modal').click();
        }

        function openPaymentDiscount(obj) {
            $('.submitPerform #discount_type').val(obj.discount_type).change();
            let other_options = JSON.parse(obj.other_options);
            $(document).find('.submitPerform input[name="payment_period_discount[thirty_days]"]').val(other_options
                .thirty_days);
            $(document).find('.submitPerform input[name="payment_period_discount[seventh_days]"]').val(other_options
                .seventh_days);
            $(document).find('.submitPerform input[name="payment_period_discount[fourteen_days]"]').val(other_options
                .fourteen_days);
            $(document).find('.submitPerform input[name="payment_period_discount[twenty_one_days]"]').val(other_options
                .twenty_one_days);
            $(document).find('.submitPerform input[name="payment_period_discount[advance_upfront]"]').val(other_options
                .advance_upfront);
            $(document).find('.submitPerform input[name="payment_period_discount[three_days]"]').val(other_options
                .three_days);
            $('.close_discount_modal').click();
        }

        function openTargetQuantityDiscount(obj) {
            $('.submitPerform #discount_type').val(obj.discount_type).change();
            $(document).find('.submitPerform .applies_to_all_item').prop('checked', obj.applies_to_all_item ? true : false)
                .change();
            let other_options = JSON.parse(obj.other_options);
            $('#add_form_here .target_discount_childs').html('');
            $.each(other_options, function(indexInArray, valueOfElement) {
                $(document).find('#add_form_here select.select-products').val(indexInArray).change();
                $(document).find(`.target_quantity[name="target_quantity[${indexInArray}]"]`).val(valueOfElement
                    .quantity)
                $(document).find(`.target_discount[name="target_discount[${indexInArray}]"]`).val(valueOfElement
                    .discount)
                // $.each(valueOfElement, function (key, val) { 
                //     // add_target_discount_childs(key,val);
                // });
            });
            $('.close_discount_modal').click();
        }

        function openTrasnportRebateDiscount(obj) {
            var v = obj.discount_type;
            $('.submitPerform #discount_type').val(v).change();
            var type = '';
            if (obj.discount_type == "Transport rebate per unit" || obj.discount_type == "Distribution Discount" || obj
                .discount_type == "Distribution Discount on Delivery") {
                type = "per_unit"
            }
            if (obj.discount_type == "Transport rebate percentage") {
                type = "percentage_of_invoice"
            }
            if (obj.discount_type == "Transport rebate per tonnage") {
                type = "per_tonnage"
            }
            let type_data = $(document).find(`#add_form_here .only_this.${type}`);

            let other_options = JSON.parse(obj.other_options);
            if (other_options.discount_target_type == "All") {
                $(document).find('.submitPerform .discount_target_type').attr('checked', true).change();
            }
            $('.hide_this_on_location_select').hide();
            $(document).find('#add_form_here .add_transport_rebate_items').html('');
            transport_rebate_store_locations = [];
            $.each(other_options.location_discounts, function(indexInArray, valueOfElement) {
                transport_rebate_store_locations.push(valueOfElement.location);
                if (type == "per_unit") {
                    var child = "<table class='table'><tr><th>Item</th><th>Per Unit</th></tr>";
                }
                if (type == "percentage_of_invoice") {
                    var child = "<table class='table'><tr><th>Item</th><th>% of Invoice</th></tr>";
                }
                if (type == "per_tonnage") {
                    var child = "<table class='table'><tr><th>Item</th><th>Per Tonnage</th></tr>";
                }
                console.log(type_data);
                $.each(type_data, function(i, v) {
                    child += `<tr>`;
                    child += `<td>${$(v).data('title')}</td>`;
                    child += `<td>
                        <input type="hidden"  value="${$(v).data('title')}" name="inventory_title[${valueOfElement.location}][${$(v).data('id')}]" >
                        <input type="number" value="" name="trade_discount[${valueOfElement.location}][${$(v).data('id')}]" class="form-control ${type}">
                        <input type="hidden"  value="${$(v).data('id')}" name="inventory_id[${valueOfElement.location}][${$(v).data('id')}]" >
                        <input type="hidden"  value="${$(v).data('stock')}" name="stock[${valueOfElement.location}][${$(v).data('id')}]" >
                        </td>`;
                    child += `</tr>`;
                });
                child += "</table>";
                let item = `
            <div
                    class="row removethisitem" style="
                            border-top: 1px solid #ddd;
                            border-bottom: 1px solid #ddd;
                            margin: 10px -15px;
                            padding: 10px 0px;
                        ">
                    
                    <div
                        class="col-sm-9">
                        <p
                            class=" change_location">
                            <b>${valueOfElement.location}</b> Location</p>
                            

                        <span class="total_childs"><b>${valueOfElement.discount.length}</b></span> Inventory Items Selected
                       
                        <br>
                            <b>Application Stage -</b><span class=" transport_application_stage_discount_change">
                                    ${valueOfElement.application_stage}</span>
                            
                        </div>
                    <div class="col-sm-3">
                        <button class="btn btn-primary  btn-sm" type="button" onclick="edit_transport_rebate_item(this); return false;">Edit</button>
                            <button class="btn btn-primary  btn-sm" type="button" onclick="removeTrasnportRebateItem(this); return;">
                            <i class="fa fa-trash"></i>
                            </button>    
                    </div>
                    <div class="show_me_on_edit_transport_rebate " style="display:none">
                        <input type="hidden" name="store_location[]" class="store_location" value="${valueOfElement.location}">
                        <div class="col-sm-12">
                            ${child}
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" 
                                        value="Application Stage" placeholder disabled
                                        class="form-control">
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Select Application Stage</label>
                                        <select name="application_stage[]"
                                        class="edit_application_stage_discount form-control">
                                        <option value="" selected disabled>-- Select Application Stage --</option>
                                        <option value="Invoice Supplier" ${valueOfElement.application_stage == 'Invoice Supplier' ? 'selected' : ''}>Invoice Supplier</option>
                                        <option value="Supplier Issues a Credit Note" ${valueOfElement.application_stage == 'Supplier Issues a Credit Note' ? 'selected' : ''}>Supplier Issues a Credit Note</option>
                                        <option value="Goods Equivalent to Transport Rebate Amount" ${valueOfElement.application_stage == 'Goods Equivalent to Transport Rebate Amount' ? 'selected' : ''}>Goods Equivalent to Transport Rebate Amount</option>
                                        <option value="Transport Rebate on Supplier Invoices" ${valueOfElement.application_stage == 'Transport Rebate on Supplier Invoices' ? 'selected' : ''}>Transport Rebate on Supplier Invoices</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="button" onclick="update_transport_rebate_items(this, '${type}'); return false;" class="btn btn-primary">
                                    Edit </button>
                            </div>

                        </div>
                    </div>

                </div>
            `;
                $(document).find('#add_form_here .add_transport_rebate_items').append(item);
                $.each(valueOfElement.discount, function(i, v) {
                    let selector =
                        `#add_form_here input[name="trade_discount[${valueOfElement.location}][${v.inventory_id}]"]`;

                    // Use jQuery to directly find the elements in the DOM and set their value
                    $(document).find(selector).val(v.discount);
                });
            });

            $('.close_discount_modal').click();

        }

        function __openTrasnportRebateDiscount(obj) {
            $('.submitPerform #discount_type').val(obj.discount_type).change();
            let other_options = JSON.parse(obj.other_options);
            if (other_options.discount_target_type == "All") {
                $(document).find('.submitPerform .discount_target_type').attr('checked', true).change();
            }
            $(document).find('#add_form_here .add_transport_rebate_items').html('');
            transport_rebate_store_locations = [];
            $.each(other_options.location_discounts, function(indexInArray, valueOfElement) {
                transport_rebate_store_locations.push(valueOfElement.location + "_" + valueOfElement
                    .inventory_title);
                let item = `
                <div
                    class="row removethisitem" style="
                            border-top: 1px solid #ddd;
                            border-bottom: 1px solid #ddd;
                            margin: 10px -15px;
                            padding: 10px 0px;
                        ">
                    
                    <div
                        class="col-sm-12">
                        <p
                            class=" change_location">
                            ${valueOfElement.location} Location</p>
                            <button class="btn btn-primary btn-sm" type="button" onclick="edit_transport_rebate_item(this); return false;">Edit</button>
                        <button class="btn btn-primary  btn-sm" type="button" onclick="removeTrasnportRebateItem(this); return;">
                           <i class="fa fa-trash"></i>
                        </button>

                    </div>
                    <div class="col-sm-4">
                            <b>Inventory-</b><span class=" inventory_title">
                                    ${valueOfElement.inventory_title}</span>
                            
                        </div>
                        <div class="col-sm-4">
                            <b>Per Unit-</b><span class=" rebate_discount_change">
                                    ${valueOfElement.per_unit_discount}</span>
                            
                        </div>

                        <div class="col-sm-4">
                            <b>% of Invoice Amount -</b><span class=" transport_rebate_invoice_discount_change">
                                    ${valueOfElement.percentage_of_invoice}</span>
                            
                        </div>

                        <div class="col-sm-4">
                            <b>Per Tonnage -</b><span class=" transport_rebate_tonnage_discount_change">
                                    ${valueOfElement.per_tonnage_discount_value}</span>
                            
                        </div>
                        <div class="col-sm-12">
                            <b>Application Stage -</b><span class=" transport_application_stage_discount_change">
                                    ${valueOfElement.application_stage}</span>
                            
                        </div>

                    <div class="show_me_on_edit_transport_rebate " style="display:none">
                        <input type="hidden" name="store_location[]" class="store_location" value="${valueOfElement.location}">
                        <input type="hidden" name="inventory_id[]" class="inventory_id" value="${valueOfElement.inventory_id}">
                        <input type="hidden" name="inventory_title[]" class="inventory_title" value="${valueOfElement.inventory_title}">
                       
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" 
                                        value="Per Unit" placeholder disabled
                                        class="form-control">
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="rebate_discount">Enter
                                            Discount
                                            Value</label>
                                        <input type="number" value="${valueOfElement.per_unit_discount}" name="per_unit_discount[]"
                                            class="edit_rebate_discount form-control">
                                        
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <label for=""
                                       >Rebate
                                        Type</label>
                                    <input type="text" 
                                        value="% of Invoice Amount" placeholder disabled
                                        class="form-control">
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="discount-value"
                                            form-control>Enter
                                            Discount
                                            Percentage</label>
                                        <input type="number"  value="${valueOfElement.percentage_of_invoice}" name="percentage_of_invoice[]"
                                            class="edit_transport_rebate_invoice_discount form-control">
                                        
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" 
                                        value="Per Tonnage" placeholder disabled
                                        class="form-control">
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Enter
                                            Discount
                                            Value</label>
                                    <input type="number" value="${valueOfElement.per_tonnage_discount_value}" name="per_tonnage_discount_value[]"
                                        class="edit_transport_rebate_tonnage_discount form-control">
                                    
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" 
                                        value="Application Stage" placeholder disabled
                                        class="form-control">
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Select Application Stage</label>
                                   
                                        <select name="application_stage[]"
                                        class="edit_application_stage_discount form-control">
                                        <option value="" selected disabled>-- Select Application Stage --</option>
                                        <option value="Invoice Supplier" ${valueOfElement.application_stage == 'Invoice Supplier' ? 'selected' : ''}>Invoice Supplier</option>
                                        <option value="Supplier Issues a Credit Note" ${valueOfElement.application_stage == 'Supplier Issues a Credit Note' ? 'selected' : ''}>Supplier Issues a Credit Note</option>
                                        <option value="Goods Equivalent to Transport Rebate Amount" ${valueOfElement.application_stage == 'Goods Equivalent to Transport Rebate Amount' ? 'selected' : ''}>Goods Equivalent to Transport Rebate Amount</option>
                                        <option value="Transport Rebate on Supplier Invoices" ${valueOfElement.application_stage == 'Transport Rebate on Supplier Invoices' ? 'selected' : ''}>Transport Rebate on Supplier Invoices</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="button" onclick="update_transport_rebate_items(this); return false;" class="btn btn-primary">
                                    Edit </button>
                            </div>

                        </div>
                    </div>

                </div>
                        `;
                $(document).find('#add_form_here .add_transport_rebate_items').append(item);
            });

            $('.close_discount_modal').click();
        }

        function add_transport_rebate_items(type) {
            let location = $(document).find('#add_form_here .transport_rebate_store_location').val();
            let all_location = $(document).find('#add_form_here .discount_target_type:checked').val();
            let type_data = $(document).find(`#add_form_here .only_this.${type}`);
            let application_stage = $(document).find('#add_form_here .application_stage_discount').val();



            if (type == "per_unit") {
                var child = "<table class='table'><tr><th>Item</th><th>Per Unit</th></tr>";
            }
            if (type == "percentage_of_invoice") {
                var child = "<table class='table'><tr><th>Item</th><th>% of Invoice</th></tr>";
            }
            if (type == "per_tonnage") {
                var child = "<table class='table'><tr><th>Item</th><th>Per Tonnage</th></tr>";
            }
            var total_childs = 0
            if (all_location == "All") {
                location = all_location;
            }
            $.each(type_data, function(indexInArray, valueOfElement) {
                if ($(valueOfElement).val()) {
                    total_childs += 1
                }

                child += `<tr>`;
                child += `<td>${$(valueOfElement).data('title')}</td>`;
                child += `<td>
                        <input type="hidden"  value="${$(valueOfElement).data('title')}" name="inventory_title[${location}][${$(valueOfElement).data('id')}]" >
                        <input type="number" value="${$(valueOfElement).val()}" name="trade_discount[${location}][${$(valueOfElement).data('id')}]" class="form-control ${type}">
                        <input type="hidden"  value="${$(valueOfElement).data('id')}" name="inventory_id[${location}][${$(valueOfElement).data('id')}]" >
                        <input type="hidden"  value="${$(valueOfElement).data('stock')}" name="stock[${location}][${$(valueOfElement).data('id')}]" >
                        </td>`;
                child += `</tr>`;
            });
            child += "</table>"


            if (!location || !application_stage || total_childs <= 0) {
                form.errorMessage('One or more fields is required to add');
                return;
            }
            if (all_location == "All" && transport_rebate_store_locations.includes(all_location)) {
                form.errorMessage('Rebate already there for all locations.');
                return;
            }
            if (transport_rebate_store_locations.includes(location)) {
                form.errorMessage('Location already been selected');
                return;
            }
            transport_rebate_store_locations.push(location);

            let item = `
            <div
                    class="row removethisitem" style="
                            border-top: 1px solid #ddd;
                            border-bottom: 1px solid #ddd;
                            margin: 10px -15px;
                            padding: 10px 0px;
                        ">
                    
                    <div
                        class="col-sm-9">
                        <p
                            class=" change_location">
                            <b>${location}</b> Location</p>
                            

                        <span class="total_childs"><b>${total_childs}</b></span> Inventory Items Selected
                       
                        <br>
                            <b>Application Stage -</b><span class=" transport_application_stage_discount_change">
                                    ${application_stage}</span>
                            
                        </div>
                    <div class="col-sm-3">
                        <button class="btn btn-primary  btn-sm" type="button" onclick="edit_transport_rebate_item(this); return false;">Edit</button>
                            <button class="btn btn-primary  btn-sm" type="button" onclick="removeTrasnportRebateItem(this); return;">
                            <i class="fa fa-trash"></i>
                            </button>    
                    </div>
                    <div class="show_me_on_edit_transport_rebate " style="display:none">
                        <input type="hidden" name="store_location[]" class="store_location" value="${location}">
                        <div class="col-sm-12">
                            ${child}
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="">Rebate
                                        Type</label>
                                    <input type="text" 
                                        value="Application Stage" placeholder disabled
                                        class="form-control">
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Select Application Stage</label>
                                        <select name="application_stage[]"
                                        class="edit_application_stage_discount form-control">
                                        <option value="" selected disabled>-- Select Application Stage --</option>
                                        <option value="Invoice Supplier" ${application_stage == 'Invoice Supplier' ? 'selected' : ''}>Invoice Supplier</option>
                                        <option value="Supplier Issues a Credit Note" ${application_stage == 'Supplier Issues a Credit Note' ? 'selected' : ''}>Supplier Issues a Credit Note</option>
                                        <option value="Goods Equivalent to Transport Rebate Amount" ${application_stage == 'Goods Equivalent to Transport Rebate Amount' ? 'selected' : ''}>Goods Equivalent to Transport Rebate Amount</option>
                                        <option value="Transport Rebate on Supplier Invoices" ${application_stage == 'Transport Rebate on Supplier Invoices' ? 'selected' : ''}>Transport Rebate on Supplier Invoices</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="button" onclick="update_transport_rebate_items(this, '${type}'); return false;" class="btn btn-primary">
                                    Edit </button>
                            </div>

                        </div>
                    </div>

                </div>
            `;
            $(document).find('#add_form_here .add_transport_rebate_items').append(item);
            $(document).find('#add_form_here .only_this.' + type).val('');
            $(document).find('#add_form_here .transport_rebate_store_location').val('').change();
            $(document).find('#add_form_here .transport_rebate_store_location').change();
        }
        $(document).on('change', '#add_form_here .transport_rebate_store_location', function(r) {
            console.log($(this).val());
            if ($(this).val() == null) {
                $(this).parents("#add_form_here").find('.hide_this_on_location_select').hide();
            } else {
                $(this).parents("#add_form_here").find('.hide_this_on_location_select').show();
            }
        })
        $(document).on('change', '#add_form_here .discount_target_type', function(r) {
            console.log($(this).val());
            if ($(this).is(':checked') && !transport_rebate_store_locations.includes('All')) {
                $(this).parents("#add_form_here").find('.hide_this_on_location_select').show();
            } else {
                $(this).parents("#add_form_here").find('.hide_this_on_location_select').hide();
            }
        })

        function update_transport_rebate_items(input, type) {
            var total_childs = 0;
            let type_data = $(input).parents(`.removethisitem`).find(`.${type}`);
            $.each(type_data, function(indexInArray, valueOfElement) {
                if ($(valueOfElement).val()) {
                    total_childs += 1
                }
            })
            $(document).find('#add_form_here .total_childs').html(total_childs);
            let application_stage = $(input).parents('.removethisitem').find('.edit_application_stage_discount').val();
            $(input).parents('.removethisitem').find('.transport_application_stage_discount_change').html(
                `${application_stage}`);

            $(input).parents('.removethisitem').find('.show_me_on_edit_transport_rebate').hide();
        }

        function edit_transport_rebate_item(input) {
            $(input).parents('.removethisitem').find('.show_me_on_edit_transport_rebate').show();

        }

        function add_to_all_items_transport_rebate(input) {
            let discount = $(input).parents("#add_form_here").find('.transport_rebate_discount_for_all').val();
            $(input).parents("#add_form_here").find('.only_this').val(discount);
            $(input).parents("#add_form_here").find('.transport_rebate_discount_for_all').val("");
        }
    </script>
@endsection
