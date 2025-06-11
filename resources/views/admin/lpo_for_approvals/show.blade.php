@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> {!! $title !!} </h3>
            </div>
            @include('message')
            {!! Form::model($row, [
                'method' => 'PATCH',
                'route' => [$model . '.update', $row->id],
                'class' => 'validate',
                'enctype' => 'multipart/form-data',
            ]) !!}
            {{ csrf_field() }}

            <?php
            $purchase_no = $row->purchaseOrder->purchase_no;
            $default_branch_id = $row->purchaseOrder->restaurant_id;
            $default_department_id = $row->purchaseOrder->wa_department_id;
            $purchase_date = $row->purchaseOrder->purchase_date;
            $getLoggeduserProfileName = $row->purchaseOrder->getrelatedEmployee->name;
            
            ?>


            <div class = "row">

                {{-- <div class = "col-sm-6">

                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                <div class="col-sm-6">
                                    {!! Form::select('restaurant_id', getBranchesDropdown(), $default_branch_id, [
                                        'class' => 'form-control ',
                                        'required' => true,
                                        'placeholder' => 'Please select branch',
                                        'id' => 'branch',
                                        'disabled' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                <div class="col-sm-6">
                                    {!! Form::select('wa_department_id', getDepartmentDropdown($default_branch_id), $default_department_id, [
                                        'class' => 'form-control ',
                                        'required' => true,
                                        'placeholder' => 'Please select department',
                                        'id' => 'department',
                                        'disabled' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div> --}}
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Purchase Order No.</label>
                                    <div class="col-sm-6">

                                        {!! Form::text('purchase_no', $purchase_no, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'required' => true,
                                            'class' => 'form-control',
                                            'readonly' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Supplier Name</label>
                                    <div class="col-sm-6">
                                        {!! Form::select('wa_supplier_id', getSupplierDropdown(), null, [
                                            'class' => 'form-control  mlselec6t',
                                            'required' => true,
                                            'id' => 'wa_supplier_id',
                                            'disabled' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                    <div class="col-sm-6">
                                        {!! Form::text('emp_name', $getLoggeduserProfileName, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'required' => true,
                                            'class' => 'form-control',
                                            'readonly' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                    <div class="col-sm-6">
                                        {!! Form::select('wa_location_and_store_id', getStoreLocationDropdown(), null, [
                                            'class' => 'form-control mlselec6t',
                                            'required' => true,
                                            'id' => 'wa_location_and_store_id',
                                            'disabled' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Purchase Date</label>
                                    <div class="col-sm-6">
                                        {!! Form::text('purchase_date', $purchase_date, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'required' => true,
                                            'class' => 'form-control',
                                            'readonly' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Delivery Type</label>
                                    {{-- {{ $row->purchaseOrder->supplier_own == 'SupplierDelivery' ? 'Supplier Delivery' : '' }}
                                    {{ $row->purchaseOrder->supplier_own == 'OwnCollection' ? 'Own Collection' : '' }} --}}
                                    <div class="col-sm-6">
                                        {!! Form::text(
                                            'delivery_type',
                                            $row->purchaseOrder->supplier_own == 'SupplierDelivery'
                                                ? 'Supplier Delivery'
                                                : ($row->purchaseOrder->supplier_own == 'OwnCollection'
                                                    ? 'Own Collection'
                                                    : ''),
                                            [
                                                'maxlength' => '255',
                                                'class' => 'form-control',
                                                'readonly' => true,
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Vehicle</label>
                                    {{-- {{ @$row->purchaseOrder->vehicle->name }}
                                    {{ @$row->purchaseOrder->vehicle->license_plate_number }} --}}
                                    <div class="col-sm-6">
                                        {!! Form::text(
                                            'vehicle',
                                            trim(
                                                implode(
                                                    ' / ',
                                                    array_filter([@$row->purchaseOrder->vehicle->name, @$row->purchaseOrder->vehicle->license_plate_number]),
                                                ),
                                            ),
                                            [
                                                'maxlength' => '255',
                                                'class' => 'form-control',
                                                'readonly' => true,
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Employee</label>
                                    {{-- {{ @$row->purchaseOrder->employee->name }} /
                                    {{ @$row->purchaseOrder->employee->id_number }} /
                                    {{ @$row->purchaseOrder->employee->phone_number }} --}}
                                    <div class="col-sm-6">
                                        {!! Form::text(
                                            'employee',
                                            trim(
                                                implode(
                                                    ' / ',
                                                    array_filter([
                                                        @$row->purchaseOrder->employee->name,
                                                        @$row->purchaseOrder->employee->id_number,
                                                        @$row->purchaseOrder->employee->phone_number,
                                                    ]),
                                                ),
                                            ),
                                            [
                                                'maxlength' => '255',
                                                'class' => 'form-control',
                                                'readonly' => true,
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            </form>
        </div>

    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">

                <div class="col-md-12 no-padding-h table-responsive">
                    <h3 class="box-title"> Purchase Order Line</h3>

                    <span id = "requisitionitemtable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Item Category</th>
                                    <th>Item No</th>
                                    <th>Description</th>
                                    <th class="align_float_right">Price List Cost</th>
                                    <th>LPO Qty</th>
                                    <th>New Qty</th>
                                    <th class="align_float_right">New LPO Amount</th>
                                    <th class="align_float_left">Free Stock</th>
                                    <th>Reason</th>

                                </tr>
                            </thead>
                            <tbody>

                                @if ($row->getRelatedItem && count($row->getRelatedItem) > 0)
                                    <?php
                                    $i = 1;
                                    $total_with_vat_arr = [];
                                    $total_price_list_cost = 0;
                                    $total_lpo_qty = 0;
                                    $total_new_qty = 0;
                                    $total_new_lpo_amount = 0;
                                    ?>
                                    @foreach ($row->getRelatedItem as $getRelatedItem)
                                        <?php
                                        $price_list_cost = $getRelatedItem?->inventory_item?->price_list_cost ?? 0.0;
                                        $lpo_qty = @$getRelatedItem->OrderItem?->supplier_quantity ?? $getRelatedItem->ordered_quantity;
                                        $new_qty = @$getRelatedItem->quantity ?? 0;
                                        $new_lpo_amount = $price_list_cost * $new_qty;
                                        
                                        $total_price_list_cost += $price_list_cost;
                                        $total_lpo_qty += $lpo_qty;
                                        $total_new_qty += $new_qty;
                                        $total_new_lpo_amount += $new_lpo_amount;
                                        ?>
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ @$getRelatedItem->inventory_item->getInventoryCategoryDetail->category_description }}
                                            </td>

                                            <td>{{ @$getRelatedItem->inventory_item->stock_id_code }}</td>
                                            <td>{{ @$getRelatedItem->inventory_item->title }}</td>
                                            <td class="align_float_right">
                                                {{ manageAmountFormat($getRelatedItem?->inventory_item?->price_list_cost) ?? 0.00 }}
                                            </td>

                                            <td class="align_float_left">
                                                {{ $lpo_qty }}
                                            </td>


                                            <td class="align_float_left">
                                                {{ @$getRelatedItem->quantity ?? 0 }}
                                            </td>
                                            <td class="align_float_right">
                                                {{ manageAmountFormat($getRelatedItem?->inventory_item?->price_list_cost *
                                                    @$getRelatedItem->quantity) }}
                                            </td>
                                            <td class="align_float_left">
                                                {{ @$getRelatedItem->free_qualified_stock }}
                                            </td>
                                            <td class="align_float_left">
                                                {{ @$getRelatedItem->reason }}
                                            </td>


                                        </tr>
                                        <?php $i++;
                                        
                                        ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="15">Do not have any item in list.</td>

                                    </tr>
                                @endif

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Totals</strong></td>
                                    <td class="align_float_right"><strong>{{ manageAmountFormat($total_price_list_cost) }}</strong></td>
                                    <td class="align_float_left"><strong>{{ $total_lpo_qty }}</strong></td>
                                    <td class="align_float_left"><strong>{{ $total_new_qty }}</strong></td>
                                    <td class="align_float_right"><strong>{{ manageAmountFormat($total_new_lpo_amount) }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </span>
                </div>
                @if ($row->status == 'Pending')
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <div class="button-group">
                                <form action="{{ route($model . '.update', $row->id) }}" method="post" class="submitMe">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" value="Approved" name="status">
                                    <button type="submit" class="btn btn-danger enableDisable"
                                        onclick="$('.enableDisable').attr('disabled',true); $(this).parents('form').submit();">
                                        <i class="fa fa-check"></i> Approve
                                    </button>
                                </form>
                                <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#modelId">
                                    <i class="fa fa-remove"></i> Reject
                                </button>
                                
                                
                               

                                   
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </section>
<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <form action="{{ route($model . '.update', $row->id) }}" method="post"
        class="submitMe">
        @csrf
        @method('PUT')
        <input type="hidden" value="Rejected" name="status">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject LPO</h5>
                    
                </div>
                <div class="modal-body">
                    <div class="form-group">
                      <label for="">Enter Reason to Reject</label>
                      <textarea name="rejection_message" id="rejection_message" class="form-control" cols="30" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Reject</button>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">

    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #last_total_row td {
            border: none !important;
        }

        .align_float_right {
            text-align: right;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
@endsection
