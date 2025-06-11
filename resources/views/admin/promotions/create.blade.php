
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> {{$inventoryItem->title}} | Create Promotion </h3>            
                <a href="{{  route('promotions.listing', $inventoryItem->id) }}" class="btn btn-primary   ">{{'<< '}}Back to Promotions</a>
            </div>

        </div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('promotions-bands.store', $inventoryItem->id) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Item Supplier</label>
                        <div class="col-sm-9">
                            <select name="supplier_id" id="supplier_id" class="mlselect" required>
                                <option value="" selected >Select Supplier</option>
                                @foreach ($inventoryItem->suppliers as $supplier )
                                    <option value="{{$supplier->id}}">
                                        {{ $supplier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Type </label>
                        <div class="col-sm-9">
                            <select name="promotion_type_id" id="promotion_type_id" class="mlselect" required>
                                <option value="" selected >Select Type</option>
                                @foreach ($promotionTypes as $type )
                                    <option value="{{$type->id}}" data-description="{{ $type->description }}">
                                        {{ $type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Group </label>
                        <div class="col-sm-9">
                            <select name="promotion_group_id" id="promotion_group_id" class="mlselect" required>
                                <option value="" selected >Select Group</option>
                                @foreach ($promotionGroups as $group )
                                    <option value="{{$group->id}}">
                                        {{ $group->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group hidden bsgy">
                        <label for="inputEmail3" class="col-sm-2 control-label">Sale Quantity</label>
                        <div class="col-sm-9" style="">
                            {!! Form::number('item_quantity', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control' ]) !!}
                        </div>
                    </div>
                    <div class="form-group hidden bsgy">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Item</label>
                        <div class="col-sm-9">
                            <select name="inventory_item" id="inventory_item" class="mlselect" required>
                                <option value="" selected >Select Item</option>
                                @foreach ($inventoryItems as $item )
                                <option value="{{$item->id}}" @selected($item->id == $inventoryItem->id) >
                                    {{ $item->stock_id_code .' '. $item->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group hidden bsgy">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Quantity</label>
                        <div class="col-sm-9">
                            {!! Form::number('promotion_quantity', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group hidden pd">
                        <label for="inputEmail3" class="col-sm-2 control-label">Current Price</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" readonly value="{{ $inventoryItem-> selling_price }}">
                        </div>
                    </div>
                    <div class="form-group hidden pd">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Price</label>
                        <div class="col-sm-9">
                            {!! Form::number('promotion_price', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">From Date</label>
                        <div class="col-sm-9">
                            {!! Form::date('from_date', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">To Date</label>
                        <div class="col-sm-9">
                            {!! Form::date('to_date', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                </div>
                    @if($inventoryItem->item_count != null)
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Apply To Split</label>
                            <div class="col-sm-9">
                                {!! Form::checkbox('apply_to_split', 'apply', false, ['class' => 'form-check-input']) !!}
                            </div>
                        </div>
                    @endif
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
            $('#promotion_type_id').change(function() {

                const selectedType = $(this).find(':selected').data('description');
                if (selectedType === @json(\App\Enums\PromotionMatrix::BSGY->value)) {
                    $('.bsgy').toggleClass('hidden');
                }else {
                    $('.bsgy').addClass('hidden');
                }

                if (selectedType === @json(\App\Enums\PromotionMatrix::PD->value)) {
                    $('.pd').toggleClass('hidden');
                } else {
                    $('.pd').addClass('hidden');
                }
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



