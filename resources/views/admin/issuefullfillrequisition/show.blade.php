@extends('layouts.admin.admin')
@section('content')
    {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
    {{ csrf_field() }}
    @if(Request::url() != URL::previous())
        <a href="{!! URL::previous() !!}" class="btn btn-primary">Back</a>
    @endif
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
            @include('message')

            <?php
            $getLoggeduserProfile = getLoggeduserProfile();
            $requisition_no = $row->requisition_no;
            $default_branch_id = $row->restaurant_id;
            $default_department_id = $row->wa_department_id;
            $requisition_date = $row->requisition_date;
            $getLoggeduserProfileName = $row->getrelatedEmployee->name;

            $default_wa_location_and_store_id = $row->wa_location_and_store_id;
            $default_to_store_id = $row->to_store_id;
            ?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Invoice No.</label>
                                <div class="col-sm-7">
                                    {!! Form::text('requisition_no',  $requisition_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                <div class="col-sm-7">
                                    {!! Form::text('emp_name',$getLoggeduserProfileName, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Invoice Date</label>
                                <div class="col-sm-7">
                                    {!! Form::text('purchase_date', $requisition_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Route</label>
                                <div class="col-sm-7">
                                    {!!Form::select('route_id',$route, NULL, ['class' => 'form-control ','id'=>'route_id'  ])!!}
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
                                    {!!Form::select('restaurant_id', getBranchesDropdown(),$default_branch_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select branch','id'=>'branch','disabled'=>true  ])!!}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                <div class="col-sm-6">
                                    {!!Form::select('wa_department_id',getDepartmentDropdown($default_branch_id), $default_department_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select department','id'=>'department','disabled'=>true  ])!!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Salesman</label>
                                <div class="col-sm-6">
                                    {!!Form::select('to_store_id',getStoreLocationDropdownByBranch($row->restaurant_id), $default_to_store_id, ['class' => 'form-control ','id'=>'to_store_id' ,'disabled'=>true ])!!}
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Customer</label>
                                <div class="col-sm-6">
                                    <span class="form-control">{{@$row->customer}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">


                <div class="col-md-12 no-padding-h">
                    <h3 class="box-title"> Invoice Line</h3>

                    <span id="requisitionitemtable">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Item Category</th>
                                <th>Item No</th>
                                <th>Description</th>
                                <th>UOM</th>
                                <th>Qty Req</th>
                                <th>Qty Issued</th>
                                <th> Price</th>
                                <th>Total Price</th>
                                <th>VAT Rate</th>
                                <th> VAT Amount</th>
                                <th>Total Price In VAT</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if($row->getRelatedItem && count($row->getRelatedItem)>0)
                                    <?php
                                    $i = 1;
                                    $total_with_vat_arr = [];
                                    ?>
                                @foreach($row->getRelatedItem as $getRelatedItem)
                                    <input type="hidden" name="related_item_ids[]" value="{{ $getRelatedItem->id }}">
                                    <tr>
                                <td>{{ $i }}</td>
                                <td>{{ @$getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>
                                <td>{{ @$getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                <td>{{ @$getRelatedItem->getInventoryItemDetail->title }}</td>
                                <td>{{ @$getRelatedItem->getInventoryItemDetail->getUnitOfMeausureDetail->title }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                                <td>

                                    {!! Form::number('delivered_quantity_'.$getRelatedItem->id,  $getRelatedItem->quantity , ['required'=>'required', 'min'=>'0','max'=> $getRelatedItem->quantity,'class'=>'form-control delivered_quantity','id'=>'delivered_quantity_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'readonly'=>true]) !!}  
                                </td>
                                <td class="align_float_right">{{ $getRelatedItem->selling_price }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->total_cost }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->vat_rate }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->vat_amount }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->total_cost_with_vat }}</td>
                                <td>{{ $getRelatedItem->note }}</td>

                            </tr>
                                        <?php
                                        $i++;

                                        $total_with_vat_arr[] = $getRelatedItem->total_cost_with_vat;
                                        ?>

                                @endforeach

                                <tr id="last_total_row">
                                <td colspan="7">
                                    <button type="submit" class="btn btn-success">Process</button>
                                </td>
                               
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="align_float_right">{{ manageAmountFormat(array_sum($total_with_vat_arr))}}</td>
                                <td></td>

                            </tr>

                            @else
                                <tr>
                                <td colspan="12">Do not have any item in list.</td>

                            </tr>
                            @endif
                        </tbody>
                    </table>
                </span>
                </div>
            </div>
        </div>


    </section>
    </form>

    @if($row->getRelatedAuthorizationPermissions && count($row->getRelatedAuthorizationPermissions)>0)
        <section class="content">
            <!-- Small boxes (Stat box) -->
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">


                    <div class="col-md-12 no-padding-h">
                        <h3 class="box-title">Approval Status</h3>

                        <span id="requisitionitemtablea">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Authorizer Name</th>
                                <th>Level</th>
                                <th>Note</th>
                                <th>Status</th>


                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $p = 1;
                            ?>
                        @foreach($row->getRelatedAuthorizationPermissions as $permissionResponse)
                            <tr>
                                <td>{{ $p }}</td>
                                <td>{{ $permissionResponse->getInternalAuthorizerProfile->name}}</td>
                                <td>{{ $permissionResponse->approve_level}}</td>
                                <td>{{ $permissionResponse->note }}</td>
                                <td>{{ $permissionResponse->status=='NEW'?'PROCESSING':$permissionResponse->status }}</td>
                            </tr>
                                <?php $p++; ?>
                        @endforeach
                        </tbody>
                    </table>
                </span>
                    </div>


                </div>
            </div>


        </section>
    @endif
    <!-- Modal -->
@endsection

@section('uniquepagestyle')

    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #last_total_row td {
            border: none !important;
        }

        #requisitionitemtable input[type=number] {
            width: 100px;

        }

        .align_float_right {
            text-align: right;
        }

    </style>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">
        var form = new Form();
        $(document).on('click', '.btnUploadData', function (e) {
            e.preventDefault();
            $('#loader-on').show();
            var postData = new FormData();

            var url = $(this).parents('form').attr('action');
            postData.append('_token', $(document).find('input[name="_token"]').val());
            $.each($('#upload_data')[0].files, function (indexInArray, valueOfElement) {
                postData.append('upload_data[' + indexInArray + ']', $('#upload_data')[0].files[indexInArray]);
            });
            $.ajax({
                type: "POST",
                url: "{{route('pos-cash-sales.esd_upload')}}",
                data: postData,
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    $('#loader-on').hide();
                    $('#upload_data').replaceWith('<input type="file" style="width: 80%" name="upload_data[]" id="upload_data" class="form-control" multiple accept="text/plain">');
                    if (response.result === -1) {
                        form.errorMessage(response.message);
                    } else {
                        form.successMessage(response.message);
                    }
                }
            });
        });

        {{--$(document).ready(function(){--}}
        {{--    var myval = $('#to_store_id').val();--}}
        {{--    $.ajax({--}}
        {{--      type: "get",--}}
        {{--      url: "{{route('sales-invoice.getsalesmanroute')}}",--}}
        {{--      data: {--}}
        {{--        'id':myval--}}
        {{--      },--}}
        {{--      success: function (response) {--}}
        {{--        if(response.result){--}}
        {{--          if(response.result === -1){--}}
        {{--            form.errorMessage(response.message);--}}
        {{--            $('#shift_id').html('').removeClass('form-control');--}}
        {{--            $('#invoices').html('').removeClass('form-control');--}}
        {{--            $('#invoicesItems').html('').removeClass('form-control');--}}
        {{--          }else{--}}
        {{--            $('#shift_id').html(response.shift_id).addClass('form-control');--}}
        {{--            $('#invoices').html(response.invoices).addClass('form-control');--}}
        {{--            $('#invoicesItems').html(response.invoicesItems).addClass('form-control');--}}
        {{--          }--}}
        {{--        }--}}
        {{--      }--}}
        {{--    });--}}
        {{--  });--}}

        $(function () {
            $(".mlselec6t").select2();
        });
        $("#inventory_category").change(function () {
            $("#item_no").val('');
            $("#unit_of_measure").val('');
            var selected_inventory_category = $("#inventory_category").val();
            manageitem(selected_inventory_category);
        });
        $("#item").change(function () {
            $("#item_no").val('');
            $("#unit_of_measure").val('');
            var selected_item_id = $("#item").val();
            getItemDetails(selected_item_id);
        });

        function getItemDetails(selected_item_id) {


            if (selected_item_id != "") {
                jQuery.ajax({
                    url: '{{route('external-requisitions.items.detail')}}',
                    type: 'POST',
                    data: {selected_item_id: selected_item_id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {

                        var obj = jQuery.parseJSON(response);
                        $("#item_no").val(obj.stock_id_code);
                        $("#unit_of_measure").val(obj.unit_of_measure);
                    }
                });
            }

        }

        function manageitem(selected_inventory_category) {

            if (selected_inventory_category != "") {
                jQuery.ajax({
                    url: '{{route('external-requisitions.items')}}',
                    type: 'POST',
                    data: {selected_inventory_category: selected_inventory_category},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $("#item").val('');
                        $("#item").html(response);
                    }
                });
            } else {
                $("#item").val('');
                $("#item").html('<option selected="selected" value="">Please select item</option>');
            }
        }


        function editRequisitionItem(link) {

            $('#edit-Requisition-Item-Model').find(".modal-content").load(link);
        }


    </script>
    <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection


