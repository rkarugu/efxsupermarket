@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;
        }
    </style>
    @include('message')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> New Item Data To Approve</h3>
                    <div>

                        <a href="{{  route('item-new-approval') }}" class="btn btn-primary   ">Back</a>
                    </div>

                </div>

            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label for="">Initiated By:</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{$row->approvalBy->name}}" readonly>

                    </div>
                    <div class="col-md-2 form-group">
                        <label for="">Date:</label>
                        <input type="text" name="date" id="date" class="form-control" value="{{ date("F j, Y, g:i a", strtotime($row->created_at)) }}" readonly>

                    </div>


                </div>


            </div>

        </div>


        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Item Details</h3>
                </div>

            </div>

            <div class="box-body">
                @php
                    $new_data = json_decode($row->new_data);
                @endphp
                <form class="validate form-horizontal" role="form" method="POST" action="{{ route('item-new-approval-approve', $row->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Stock ID Code</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('stock_id_code',$new_data->stock_id_code, ['maxlength'=>'255','placeholder' => 'Stock ID Code', 'required'=>true, 'class'=>'form-control', 'readonly'=>true]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Item Title</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('title', $new_data->title, ['maxlength'=>'255','placeholder' => 'Item Title', 'required'=>true, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Description</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('description', $new_data->description, ['maxlength'=>'255','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Inventory Category</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(), $new_data->wa_inventory_category_id, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control wa_inventory_category_id mlselec6t']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Inventory Sub Category</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('item_sub_category_id', getInventorySubCategoryList() ,$new_data->item_sub_category_id, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control item_sub_category_id']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Preferred Supplier</label>
                                    <div class="col-sm-7">
                                        @if(!empty($new_data->suppliers))
                                            {!! Form::select('suppliers[]', $suppliers, $new_data->suppliers, ['class' => 'form-control selector_selects2', 'required' => false, 'multiple' => true]) !!}
                                        @else
                                            {!! Form::select('suppliers[]', $suppliers, null, ['class' => 'form-control selector_selects2', 'required' => false, 'multiple' => true]) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Selling Price Inc Vat</label>
                                    <div class="col-sm-7">
                                        {!! Form::number('selling_price', $new_data->selling_price, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Standard Cost</label>
                                    <div class="col-sm-7">
                                        {!! Form::number('standard_cost', $new_data->standard_cost, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Price List Cost</label>
                                    <div class="col-sm-7">
                                        {!! Form::number('price_list_cost', $new_data->price_list_cost ?? 0, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Max Order Quantity (CAP)</label>
                                    <div class="col-sm-7">
                                        {!! Form::number('max_order_quantity', $new_data->max_order_quantity, ['min'=>'0', 'class'=>'form-control']) !!}  
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="col-md-6">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Margin Type</label>
                                    <div class="col-sm-7">
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline" style="margin-right:10px;">
                                                <input class="form-check-input" type="radio" name="margin_type" id="marginPercentage" value="1"
                                                       @if (!isset($new_data->margin_type) || $new_data->margin_type == 1) checked @endif required>
                                                <label class="form-check-label" for="marginPercentage">Percentage</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="margin_type" id="marginValue" value="0" @if (($new_data->margin_type ?? 1) == 0) checked @endif >
                                                <label class="form-check-label" for="marginValue">Value</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Min Margin</label>
                                    <div class="col-sm-7">
                                        {!! Form::number('percentage_margin', $new_data->percentage_margin, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Actual Margin</label>
                                    <div class="col-sm-7">
                                        {!! Form::number('actual_margin', $new_data?->actual_margin ?? '', ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Tax Category</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('tax_manager_id',$all_taxes ,$new_data->tax_manager_id, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Pack Size</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('pack_size_id',$packSizes ,$new_data->pack_size_id, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Alt Code</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('alt_code',$new_data->alt_code, ['maxlength'=>'255','placeholder' => 'Alt Code', 'required'=>false, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Packaged Volume (metres cubed)</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('packaged_volume',null, ['maxlength'=>'255','placeholder' => 'Packaged Volume (metres cubed)', 'required'=>false, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div> --}}

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Gross Weight (KGs)</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('gross_weight',$new_data->gross_weight, ['maxlength'=>'255','placeholder' => 'Gross Weight (KGs)', 'required'=>false, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Net Weight (KGs)</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('net_weight',$new_data->net_weight, ['maxlength'=>'255','placeholder' => 'Net Weight (KGs))', 'required'=>false, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">HS Code</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('hs_code',$new_data->hs_code, ['maxlength'=>'100','placeholder' => 'HS Code', 'required'=>false, 'class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Is Hamper</label>
                                    <div class="col-sm-7">
                                        {!! Form::checkbox('is_hamper',1, $new_data->is_hamper ?? 0) !!}
                                    </div>
                                </div>
                            </div> --}}

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Image</label>
                                    <div class="col-sm-7">
                                        <div style="margin-bottom: 10px">
                                            <img class="img-responsive" src="{{ public_path("uploads/inventory_items/{$new_data->stock_id_code}.jpg") }}" alt="{{$new_data->stock_id_code}}">
                                        </div>
                                        {!! Form::file('image', null, ['class'=>'form-control']) !!}
                                    </div>
                                </div>
                            </div>


                        </div>


                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"></h3>
                        <div>
                            <div>
                                @if(isset($permission[$pmodule.'___item-approval']) || $permission == 'superadmin')
                                    <button type="submit" class="btn btn-success">Approve</button>
                                    {{-- <a href = "#" class = "btn btn-success">Approve</a> --}}
                                @endif
                                @if(isset($permission[$pmodule.'___item-approval']) || $permission == 'superadmin')
                                    <a href="{{route('item-new-approval-reject', $row->id)}}" class="btn btn-success">Reject</a>
                                @endif
                            </div>

                        </div>

                    </div>
                </form>


            </div>

        </div>


    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
    <div id="loader-on"
         style="
            position: fixed;
            top: 0;
            text-align: center;
            display: block;
            z-index: 999999;
            width: 100%;
            height: 100%;
            background: #000000b8;
            display:none;
        "
         class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    {{-- <script src="{{ asset('js/multistep-form.js') }}"></script> --}}
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $(".mlselec6t").select2();
            $(".selector_selects2").select2();
            $('.wa_inventory_category_id').change(function (e) {
                $('.item_sub_category_id option:selected').remove();
            });
            $('.item_sub_category_id').select2(
                {
                    placeholder: 'Select Sub Category',
                    ajax: {
                        url: '{{route("inventory-categories.search_sub_categories")}}',
                        dataType: 'json',
                        type: "GET",
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                id: $('.wa_inventory_category_id option:selected').val()
                            };
                        },
                        processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.title};
                            });
                            return {
                                results: res
                            };
                        }
                    },
                });
        });
        $(document).ready(function () {

            $('.wa_inventory_category_id').select2();
            $('#supplier-id').select2();
        });
    </script>
    <script type="text/javascript">

    </script>
@endsection
