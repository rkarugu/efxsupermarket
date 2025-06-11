
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title">  Edit Promotion </h3>            
                <a href="{{  route('promotions.listing', $promotion->inventory_item_id) }}" class="btn btn-primary   ">{{'<< '}}Back to Promotions</a>
             
            </div>
        </div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('promotions-bands.update', $promotion->id) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Item Supplier</label>
                        <div class="col-sm-9">
                            <select name="supplier_id" id="supplier_id" class="mlselect" required>
                                <option value="" selected >Select Supplier</option>
                                @foreach ($inventoryItem->suppliers as $supplier )
                                    <option value="{{$supplier->id}}" {{ $supplier->id == $promotion->supplier_id ? 'selected' : '' }}>
                                        {{ $supplier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Type </label>
                        <div class="col-sm-9">
                            <select name="promotion_type_id" id="promotion_type_id" class="mlselect" required>
                                @foreach ($promotionTypes as $type )
                                    <option value="{{$type->id}}" {{ $type->id == $promotion->promotion_type_id ? 'selected' : '' }} data-description="{{ $type->description }}">
                                        {{ $type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Group </label>
                        <div class="col-sm-9">
                            <select name="promotion_group_id" id="promotion_group_id" class="mlselect" required>
                                @foreach ($promotionGroups as $group )
                                    <option value="{{$group->id}}"  {{ $group->id == $promotion->promotion_group_id ? 'selected' : '' }} >
                                        {{ $group->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group hidden bsgy">
                    <label for="inputEmail3" class="col-sm-2 control-label">Sale Quantity</label>
                    <div class="col-sm-9" style="">
                        {!! Form::number('item_quantity', $promotion->sale_quantity, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                    <div class="form-group hidden bsgy">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotiom Item</label>
                        <div class="col-sm-9">
                            <select name="inventory_item" id="inventory_item" class="mlselect">
                                @foreach ($inventoryItems as $item )
                                <option value="{{$item->id}}" {{ $item->id == $promotion->promotion_item_id ? 'selected' : '' }} >{{ $item->stock_id_code .' '. $item->title}}</option>

                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group hidden bsgy">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Quantity</label>
                        <div class="col-sm-9">
                            {!! Form::number('promotion_quantity', $promotion->promotion_quantity, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group hidden hidden pd">
                        <label for="inputEmail3" class="col-sm-2 control-label">Current Price</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" readonly value="{{ $promotion-> current_price }}">
                        </div>
                    </div>
                    <div class="form-group hidden hidden pd">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Price</label>
                        <div class="col-sm-9">
                            {!! Form::number('promotion_price', $promotion->promotion_price, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">From Date</label>
                        <div class="col-sm-9">
                            {!! Form::date('from_date', \Carbon\Carbon::parse($promotion->from_date)->toDateString(), ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">To Date</label>
                        <div class="col-sm-9">
                            {!! Form::date('to_date', \Carbon\Carbon::parse($promotion->to_date)->toDateString(), ['maxlength'=>'255','placeholder' => '0', 'class'=>'form-control']) !!}
                        </div>

                    </div>
                    </div>
            </div>  
            <div class="box-footer" >
                <button type="submit" class="btn btn-primary" >Submit</button>
            </div>
        </form>
    </div>
</section>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>



    <script type="text/javascript" class="init">
        $(document).ready(function () {

            function checkPromotionType() {
                const selectedType = $('#promotion_type_id').find(':selected').data('description');
                console.log(selectedType)
                if (selectedType === @json(\App\Enums\PromotionMatrix::BSGY->value)) {
                    $('.bsgy').toggleClass('hidden', false);
                } else {
                    $('.bsgy').addClass('hidden');
                }

                if (selectedType === @json(\App\Enums\PromotionMatrix::PD->value)) {
                    $('.pd').toggleClass('hidden', false);
                } else {
                    $('.pd').addClass('hidden');
                }
            }

            // Run the check on page load
            checkPromotionType();

            // Run the check on promotion_type_id change
            $('#promotion_type_id').change(function() {
                checkPromotionType();
            });


            $('#create_datatable1').DataTable({
                pageLength: "100",
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
@endsection



