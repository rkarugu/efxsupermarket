@extends ('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Edit Items Resolve Branch Requisition To LPO </h3>
            </div>
            @include('message')
            {!! Form::model($row, [
                'method' => 'PATCH',
                'route' => [$model . '.update', $row->slug],
                'class' => 'validate',
                'enctype' => 'multipart/form-data',
            ]) !!}
            {{ csrf_field() }}

            <?php
            $purchase_no = $row->purchase_no;
            $default_branch_id = $row->restaurant_id;
            $default_department_id = $row->wa_department_id;
            $requisition_date = $row->requisition_date;
            $getLoggeduserProfileName = $row->getrelatedEmployee->name;
            $default_wa_location_and_store_id = $row->wa_store_location_id;
            $default_unit_of_measures_id = $row->wa_unit_of_measures_id;
            $wa_supplier_id = $row->wa_supplier_id;
            
            ?>



            <div class="row">

                <div class="col-sm-6">
                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Purchase Requisition No.</label>
                                <div class="col-sm-7">


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

                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                <div class="col-sm-7">
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

                    <div class="row">

                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Requisition Date</label>
                                <div class="col-sm-7">
                                    {!! Form::text('purchase_date', $requisition_date, [
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

                    <div class="row">

                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Priority Level</label>
                                <div class="col-sm-6">
                                    <span class="form-control">{{ @$row->priority_level->title }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Note</label>
                                <div class="col-sm-7">
                                    <span class="form-control">{{ $row->note }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="row">

                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                <div class="col-sm-6">
                                    <span class="form-control">{{ @$row->getBranch->name }}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                <div class="col-sm-6">
                                    <span class="form-control">{{ @$row->getDepartment->department_name ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                <div class="col-sm-6">
                                    <span class="form-control">{{ @$row->store_location->location_name }} </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Bin Location</label>
                                <div class="col-sm-6">
                                    <span class="form-control">{{ @$row->unit_of_measure->title }} </span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Supplier</label>
                                <div class="col-sm-6">
                                    <span class="form-control">{{ @$row->supplier->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </form>
        </div>



    </section>


    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">


                <div class="col-md-12 no-padding-h">
                    <h3 class="box-title"> Requisition Line</h3>

                    <span id = "requisitionitemtable">
                        <form method="POST" action="{{ route('resolve-requisition.edit-item') }}" class="item-update-form">
                            @csrf
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Item No</th>
                                        <th>Description</th>
                                        <th>UOM</th>
                                        <th>Sales</th>
                                        <th>Re-Order Level</th>
                                        <th>Max Stock</th>
                                        <th>QOH</th>
                                        <th>QOO</th>
                                        <th>Qty Req</th>
                                        <th> Cost</th>
                                        <th>Total Cost</th>
                                        <th>VAT Rate</th>
                                        <th> VAT Amount</th>
                                        <th>Total Cost In VAT</th>
                                        <th>Note</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    @if ($row->getRelatedItem && count($row->getRelatedItem) > 0)
                                        <?php $i = 1; ?>
                                        @foreach ($row->getRelatedItem as $getRelatedItem)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ @$getRelatedItem->item_no }}</td>
                                                <td>{{ @$getRelatedItem->getInventoryItemDetail->title }}</td>
                                                <td>{{ @$getRelatedItem->getInventoryItemDetail->pack_size->title }}</td>
                                                <td>{{ number_format(abs($getRelatedItem->totalSales), 2) }}</td>
                                                <td>{{ $getRelatedItem->reorder }}</td>
                                                <td>{{ $getRelatedItem->max }}</td>
                                                <td>{{ abs($getRelatedItem->qoh) }}</td>
                                                <td>{{ $getRelatedItem->qoo }}</td>
                                                <td class="align_float_right">
                                                    <input type="number" class="form-control quantity-input"
                                                        data-id="{{ $i }}"
                                                        data-item-id="{{ $getRelatedItem->id }}"
                                                        value="{{ $getRelatedItem->quantity }}" min="1"
                                                        name="items[{{ $getRelatedItem->id }}][quantity]">
                                                </td>
                                                <td class="align_float_right" id="standard_cost_{{ $i }}">
                                                    {{ $getRelatedItem->standard_cost }}</td>
                                                <td class="align_float_right" id="total_cost_{{ $i }}">
                                                    {{ $getRelatedItem->total_cost }}</td>
                                                <td class="align_float_right" id="vat_rate_{{ $i }}">
                                                    {{ $getRelatedItem->vat_rate }}</td>
                                                <td class="align_float_right" id="vat_amount_{{ $i }}">
                                                    {{ $getRelatedItem->vat_amount }}</td>

                                                <td class="align_float_right"
                                                    id="total_cost_with_vat_{{ $i }}">
                                                    {{ $getRelatedItem->total_cost_with_vat }}</td>
                                                <td class="align_float_right">
                                                    <input type="text" class="form-control quantity-input"
                                                        data-id="{{ $i }}"
                                                        data-item-id="{{ $getRelatedItem->id }}"
                                                        value="{{ $getRelatedItem->note }}" min="0"
                                                        name="items[{{ $getRelatedItem->id }}][note]" readonly>
                                                </td>

                                            </tr>
                                            <input type="hidden" name="items[{{ $getRelatedItem->id }}][total_cost]"
                                                id="total_cost_hidden_{{ $i }}"
                                                value="{{ $getRelatedItem->total_cost }}">
                                            <input type="hidden" name="items[{{ $getRelatedItem->id }}][vat_amount]"
                                                id="vat_amount_hidden_{{ $i }}"
                                                value="{{ $getRelatedItem->vat_amount }}">
                                            <input type="hidden"
                                                name="items[{{ $getRelatedItem->id }}][total_cost_with_vat]"
                                                id="total_cost_with_vat_hidden_{{ $i }}"
                                                value="{{ $getRelatedItem->total_cost_with_vat }}">

                                            <?php $i++; ?>
                                        @endforeach
                                    @endif



                                </tbody>

                            </table>
                            <button type="submit" id="submit-updates" class="btn btn-primary ">Update</button>
                        </form>

                </div>
            </div>
        </div>
    </section>
    <div class="modal" id="edit-Requisition-Item-Model" class="modal fade" role="dialog"
        aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

            </div>
        </div>
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
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $(".mlselec6t").select2();
        })
    </script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = document.querySelectorAll('.quantity-input');

            quantityInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const id = this.dataset.id;
                    const quantity = parseFloat(this.value);
                    const standardCost = parseFloat(document.getElementById('standard_cost_' + id)
                        .innerText);
                    const vatRate = parseFloat(document.getElementById('vat_rate_' + id).innerText);

                    const totalCostElement = document.getElementById('total_cost_' + id);
                    const totalCostWithVatElement = document.getElementById('total_cost_with_vat_' +
                        id);
                    const vatAmountElement = document.getElementById('vat_amount_' + id);

                    if (!isNaN(quantity) && !isNaN(standardCost) && !isNaN(vatRate)) {
                        const totalCost = quantity * standardCost;
                        const costNoTax = (totalCost * 100) / (vatRate + 100);
                        const vatCost = totalCost - costNoTax;

                        totalCostElement.innerText = costNoTax.toFixed(2);
                        totalCostWithVatElement.innerText = totalCost.toFixed(2);
                        vatAmountElement.innerText = vatCost.toFixed(2);

                        document.getElementById('total_cost_hidden_' + id).value = costNoTax
                            .toFixed(2);
                        document.getElementById('vat_amount_hidden_' + id).value = vatCost.toFixed(
                            2);
                        document.getElementById('total_cost_with_vat_hidden_' + id).value =
                            totalCost.toFixed(2);
                    }
                });
            });
        });

        $(document).ready(function() {
            var form = new Form()
            $(document).on('submit', '.item-update-form', function(e) {
                e.preventDefault();

                let submitBtn = $('#submit-updates');
                let originalSubmitBtnText = submitBtn.text();

                submitBtn.prop('disabled', true).text('Processing...');

                let submitForm = $(this);

                $.ajax({
                    url: submitForm.attr('action'),
                    method: submitForm.attr('method'),
                    data: submitForm.serialize(),
                    success: function(response) {
                        form.successMessage('LPO Items updated Successfully.')
                        setTimeout(() => {
                            window.location.href = response.redirect_url;
                        }, 3000);
                    },
                    error: function(err) {
                        let errorMessage = '';
                        if (err?.responseJSON?.errors) {
                            $.each(err.responseJSON.errors, function(key, value) {
                                errorMessage += value.join('<br>') + '<br>';
                            });
                        }else if (err?.responseJSON?.error){
                            errorMessage = err.responseJSON.error;
                        }else {
                            errorMessage = 'Something went wrong'
                        }
                        Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: 'Something went wrong.',
                            });

                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalSubmitBtnText);
                    }
                });
            });
        });
    </script>
@endsection
