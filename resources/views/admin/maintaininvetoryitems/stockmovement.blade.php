@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <?php
        $logged_user_info = getLoggeduserProfile();
        $my_permissions = $logged_user_info->permissions;
        ?>
        <div class="box box-primary">
            <div class="box-header with-border ">
                <div class="d-flex justify-content-between align-items-center">

                    <h3 class="box-title">{{ "$inventoryItem->stock_id_code $inventoryItem->title" }} - Stock Movements</h3>
                    <a style="float:right;" class="btn btn-primary" href="{{route($model.'.index')}}">{{ "<< " }}Back</a>

                </div>
            </div>

            <div class="box-body">
                <div>
                    @if($model == "maintain-suppliers")
                        <form action="{{route('maintain-items.supplier-stock-movements',['stockIdCode'=>$StockIdCode])}}" method="get">
                            @else
                                <form action="{{route('maintain-items.'.$formurl,['stockIdCode'=>$StockIdCode])}}" method="get">
                                    @endif
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Stock Movement From</label>
                                                <input type="date" name="from" id="from" class="form-control" value="{{request()->from}}" placeholder="" aria-describedby="helpId">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Stock Movement To</label>
                                                <input type="date" name="to" id="to" class="form-control" value="{{request()->to}}" aria-describedby="helpId">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Location</label>
                                                <select type="date" name="location" id="location" class="form-control mlselec6t">
                                                    @foreach ($location as $item)
                                                        <option value="{{$item->id}}" {{(request()->location && request()->location == $item->id) ? 'selected' : NULL}}>{{$item->location_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Type</label>
                                                <select name="move_type" id="move-type" class="form-control mlselec6t">
                                                    <option value="" selected disabled>Select</option>
                                                    <option value="adjustment" {{ request()->move_type == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                                    <option value="cash-sales" {{ request()->move_type == 'cash-sales' ? 'selected' : '' }}>Return</option>
                                                    <option value="delivery-note" {{ request()->move_type == 'delivery-note' ? 'selected' : '' }}>Delivery Note</option>
                                                    <option value="ingredients-booking" {{ request()->move_type == 'ingredients-booking' ? 'selected' : '' }}>Ingredients Booking</option>
                                                    <option value="internal-requisition-store-c" {{ request()->move_type == 'internal-requisition-store-c' ? 'selected' : '' }}>Internal Requisition Store C</option>
                                                    <option value="purchase" {{ request()->move_type == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                                    <option value="recieve-stock-store-c" {{ request()->move_type == 'recieve-stock-store-c' ? 'selected' : '' }}>Recieve Stock Store C</option>
                                                    <option value="return-from-store" {{ request()->move_type == 'return-from-store' ? 'selected' : '' }}>Return From Store</option>
                                                    <option value="sales-invoice" {{ request()->move_type == 'sales-invoice' ? 'selected' : '' }}>Sales Invoice</option>
                                                    <option value="stock-break" {{ request()->move_type == 'stock-break' ? 'selected' : '' }}>Stock Break</option>
                                                    <option value="transfer" {{ request()->move_type == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                                </select>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="" style="color: white !important;">Actions</label>
                                                <div class="d-flex">
                                                    @if (count(request()->query()))
                                                        <a href="{{ route('maintain-items.stock-movements', $inventoryItem->stock_id_code) }}" class="btn btn-warning ml-12">Clear Filters</a>
                                                    @endif
                                                    <button type="submit" value="filter" name="type" class="btn btn-warning ml-12">Filter</button>
                                                    <button type="submit" value="pdf" name="type" class="btn btn-primary ml-12">Stock Card</button>
                                                    <input type="submit" value="Excel" name="type" class="btn btn-primary ml-12" >
                                                    <button type="button" value="print" name="type" onclick="printStockCard(this); return false;" class="btn btn-default ml-12"><i class="fa fa-print"
                                                                                                                                                                                   aria-hidden="true"></i>
                                                    </button>
                                                    @if ($logged_user_info->role_id == 1 )
                                                     <input type="submit" value="Stock Moves" name="type" class="btn btn-primary ml-12" >
                                                    @endif



                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                </div>

                <hr>

                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_no_ordering">
                        <thead>
                        <tr>
                            <th style="width: 15%;">Date</th>
                            <th>User Name</th>
                            <th>Store Location</th>
                            <th>Qty In</th>
                            <th>Qty Out</th>
                            <th>New QOH</th>
                            <th>Selling Price</th>
                            <th>Refrence</th>
                            <th>Document No</th>
                            <th>Type</th>

                            {{-- <th  class="noneedtoshort" >Action</th> --}}






                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lists) && !empty($lists))
                                <?php $b = 1; ?>
                            @foreach($lists as $key => $list)
                                @php
                                    $new_qoh = @$list->new_qoh;
                                    if (strpos($list->document_no, 'GRN') !== false) {
                                        if($key != 0){
                                            if (isset($lists[$key+1])) {
                                                $new_qoh = $lists[$key+1]->new_qoh + @$list->new_qoh;
                                            } 
                                        } 
                                    }
                                @endphp
                                <tr>
                                    <td style="width: 15%;">{!! date('d-m-Y H:i:s',strtotime(@$list->created_at)) !!}</td>
                                    <td>{!! ucfirst(@$list->getRelatedUser->name) !!}</td>
                                    <td>{!! isset($list->getLocationOfStore->location_name) ? ucfirst($list->getLocationOfStore->location_name) : '' !!}</td>
                                    <td>{!! (($list->qauntity >= 0) ? +$list->qauntity : NULL) !!}</td>
                                    <td>{!! (($list->qauntity < 0) ? -$list->qauntity : NULL) !!}</td>
                                    <td>{!! manageAmountFormat(@$new_qoh) !!}</td>
                                    <td>{!! manageAmountFormat(@$list->selling_price) !!}</td>
                                    <td>{!! @$list->refrence !!}</td>
                                    <td>{!! @$list->document_no !!}</td>
                                    <td>{!! getStockMoveType($list) !!}</td>

                                    {{-- <td class = "action_crud">

                                        <span>
                                             <a style="font-size: 16px;"  href="{{ route($model.'.stock-movements.gl-entries', [$list->id, $StockIdCode]) }}" ><i class="fa fa-list" title= "View GL Entries"></i></a>
                                         </span>
                                    </td>
                                            --}}


                                </tr>
                                    <?php $b++; ?>
                            @endforeach
                        @endif


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #create_datatable_no_ordering tr:nth-child(even) td {
            background: #ddd;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            $(".mlselec6t").select2();
            
            $('#move-type').select2({
                placeholder: 'Select',
                allowClear: true
            });
        });

        function printStockCard(input) {
            var url = "{{route('maintain-items.stock-movements',['stockIdCode'=>$StockIdCode])}}?" + $(input).parents('form').serialize() + '&type=print';
            print_this(url);

        }
    </script>
@endsection
