@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        {!! Form::open(['url' => route('delivery-notes.store'), 'method' => 'post', 'class' => 'validate']) !!}
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <h4 class="box-title">Post Delivery Note</h4>
            </div>
            <div class="box-body">
                <div class = "row">
                    <div class = "col-sm-6">
                        <div class="form-group">
                            <div class="row">
                                <label for="inputEmail3" class="col-sm-5 control-label">Purchase Order No.</label>
                                <div class="col-sm-7">
                                    {!! Form::text('purchase_no', $order->purchase_no, [
                                        'maxlength' => '255',
                                        'placeholder' => '',
                                        'required' => true,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                <div class="col-sm-7">
                                    <select name="branch" id="branch" class="form-control" disabled>
                                        <option value="">Select Option</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" @selected($branch->id == $order->restaurant_id)>
                                                {{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="supplier_invoice_no" class="col-sm-5 control-label">Supplier Invoice No.</label>
                                <div class="col-sm-7">
                                    {!! Form::text('supplier_invoice_no', null, [
                                        'class' => 'form-control',
                                        'id' => 'supplier_invoice_no',
                                        'required' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="vehicle_reg_no" class="col-sm-5 control-label">Vehicle Reg No</label>
                                <div class="col-sm-7">
                                    {!! Form::text('vehicle_reg_no', null, [
                                        'class' => 'form-control',
                                        'id' => 'vehicle_reg_no',
                                        'required' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="row">
                                <label for="supplier" class="col-sm-5 control-label">Supplier Name</label>
                                <div class="col-sm-7">
                                    <select name="supplier" id="supplier" class="form-control" disabled>
                                        <option value="">Select Option</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" @selected($supplier->id == $order->wa_supplier_id)>
                                                {{ $supplier->name }}({{ $supplier->supplier_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="location" class="col-sm-5 control-label">Store location</label>
                                <div class="col-sm-7">
                                    <select name="location" id="location" class="form-control" disabled>
                                        <option value="">Select Option</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" @selected($location->id == $order->wa_location_and_store_id)>
                                                {{ $location->location_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="cu_invoice_number" class="col-sm-5 control-label">CU Invoice Number</label>
                                <div class="col-sm-7">
                                    {!! Form::text('cu_invoice_number', null, [
                                        'class' => 'form-control',
                                        'id' => 'cu_invoice_number',
                                        'required' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="receive_note_doc_no" class="col-sm-5 control-label">Delivery Note</label>
                                <div class="col-sm-7">
                                    {!! Form::text('receive_note_doc_no', null, [
                                        'class' => 'form-control',
                                        'id' => 'receive_note_doc_no',
                                        'required' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Purchase Order Line</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Item No</th>
                            <th>Description</th>
                            <th>Item Category</th>
                            <th>Bin Location</th>
                            <th class="text-right">QTY</th>
                            <th class="text-right">Incl Price</th>
                            <th class="text-right">VAT</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->purchaseOrderItems as $item)
                            <tr>
                                <td>{{ $item->item_no }}</td>
                                <td>{{ $item->inventoryItem->title }}</td>
                                <td>{{ $item->inventoryItem->category->category_description }}</td>
                                <td>{{ $item->get_unit_of_measure->title }} </td>
                                <td class="text-right">{{ number_format($item->quantity) }}</td>
                                <td class="text-right">{{ manageAmountFormat($item->order_price) }}</td>
                                <td class="text-right">{{ manageAmountFormat($item->vat_amount) }}</td>
                                <td class="text-right">{{ manageAmountFormat($item->total_cost_with_vat) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Submit
                </button>
                <a href="{{ route('delivery-notes.index') }}" type="submit" class="btn btn-primary">
                    <i class="fa fa-chevron-left"></i> Back
                </a>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("select.form-control").select2();
        });
    </script>
@endpush
