@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Edit Demand</h4>
                </div>
            </div>
            <form action="{{ route('trade-discount-demands.update', $demand) }}" method="post">
                @csrf
                @method('put')
                <div class="box-body">
                    <table class="table no-border">
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="row">
                                    <label class="col-sm-4">Demand No</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="demand_no" id="demand_no" disabled
                                            value="{{ $demand->demand_no }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <label class="col-sm-4">Supplier</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="demand_no" id="demand_no" disabled
                                            value="{{ $demand->supplier->name }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="row">
                                    <label class="col-sm-4">Reference</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="supplier_reference" id="supplier_reference"
                                            value="{{ $demand->supplier_reference }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <label class="col-sm-4">CU Invoice No</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="cu_invoice_number" id="cu_invoice_number"
                                            value="{{ $demand->cu_invoice_number }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="row">
                                    <label class="col-sm-4">Note Date</label>
                                    <div class="col-sm-8">
                                        <input type="date" name="note_date" id="note_date"
                                            value="{{ $demand->note_date }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <label class="col-sm-4">Memo</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="memo" id="memo" value="{{ $demand->memo }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </table>
                    <h4>Items</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($demand->items as $item)
                                <tr>
                                    <td>{{ $item->discount->id }}</td>
                                    <td>{{ $item->discount->supplier_invoice_number }}</td>
                                    <td>{{ $item->discount->invoice_date }}</td>
                                    <td>{{ $item->discount->description }}</td>
                                    <td class="text-right">{{ manageAmountFormat($item->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th class="text-right">{{ manageAmountFormat($demand->items->sum('amount')) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="text-right">
                        <a href="{{ route('maintain-suppliers.vendor_centre', $demand->supplier->supplier_code) }}"
                            class="btn btn-primary">
                            &times; Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
