
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <br>
                            @include('message')

                            {!! Form::model(null, ['method' => 'post','route' => ['admin.approve.transfer-order', $from_order->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
                              {{ csrf_field() }}


                            {!! Form::hidden('request_type', 'order_to_table') !!}  
                            {!! Form::hidden('to_table_slug', $to_table->slug) !!}

                            <table class="table table-bordered table-hover">
                              
                            <tr>
                              <td> <span class="headeingg">From Order #{!! $from_order->id !!} </span>

                              <table style="width: 100%;border-right: none !important;" class="inner_tabel">

                              <?php $i =1;?>
                              @foreach($from_order->getAssociateItemWithOrder as $from_order_item)
                              <tr>
                                <td>{!! $i !!}.</td>
                                <td><input type="checkbox" name = "ordered_item_id[{!! $from_order_item->id !!}]"/></td>
                                <td>{!! $from_order_item->item_title !!}</td>

                                 <td>

                                   {!! Form::number('selected_qty_'.$from_order_item->id, $from_order_item->item_quantity,['class'=>'form-control','style'=>'width:30%;','min'=>1,'max'=>$from_order_item->item_quantity,'required'=>true]) !!}  

                                     {!! Form::hidden('original_qty_'.$from_order_item->id,$from_order_item->item_quantity) !!}  
                                 </td>
                              </tr>
                              <?php $i++ ;?>
                              @endforeach

                                @foreach($from_order->getAssociateOffersWithOrder as $from_order_offer)
                              <tr>
                                <td>{!! $i !!}.</td>
                                <td><input type="checkbox" name = "ordered_offer_id[{!! $from_order_offer->id !!}]"/></td>
                                <td>{!! $from_order_offer->offer_title !!}</td>
                              </tr>
                              <?php $i++ ;?>
                              @endforeach

                              </table>
                             
                             
                              
                             
                              
                              

                              

                              </td>
                              <td><span class="headeingg">To Table #{!! $to_table->name !!}</span>
                           

                              </td>

                            </tr>

                            <tr>
                              <td colspan="2"><button class="btn btn-success">Submit</td></button>

                            </tr>


                            </table>

                            </form>
                             

                         
                           
                        </div>
                    </div>


    </section>
 

 
@endsection

@section('uniquepagestyle')
  <style type="text/css">
       .table td {
  font-size: 13px;
}
.box-header.with-border.no-padding-h-b {
  min-height: 300px;
  padding-top: 100px;
  font-size: 18px;
}
.select2-container .select2-selection--single .select2-selection__rendered {

  margin-top: -6px;
  height: 100px !important;
}
.inner_tabel {
  
  margin-top:20px ; 
}
.inner_tabel td{
  border: none !important;
  
}
.headeingg{
  color:#f80202;
  font-size: 20px;
}

   </style>
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')




<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">




</script>

@endsection



