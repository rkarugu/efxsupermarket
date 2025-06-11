
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <br>
                            @include('message')

                             {!! Form::model(null, ['method' => 'post','route' => ['admin.post.void-items-from-bill', $fromBill->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
                              {{ csrf_field() }}    

                                 {!! Form::hidden('from_bill_slug',$fromBill->slug) !!}  
                                

                            <table class="table table-bordered table-hover">
                              
                            <tr>
                              <td width="100%"> <span class="headeingg">Bill #{!! $fromBill->id !!} </span>

                              <table style="width: 100%;border-right: none !important;" class="inner_tabel">


                              @if($fromBill->getAssociateOrdersWithBill && count($fromBill->getAssociateOrdersWithBill)>0)
                              @foreach($fromBill->getAssociateOrdersWithBill as $fromOrders)

                            <?php // dd($fromOrders->getAssociateOrderForBill->getAssociateItemWithOrder); ?>

                              <tr>
                                <td style="text-align: left;font-size: 14px;"><b>Order No. {{ manageOrderidWithPad($fromOrders->order_id)}}</b></td>
                              </tr>


                             

                              <tr>

                                <td>
                                 @if(isset($fromOrders->getAssociateOrderForBill->getAssociateItemWithOrder))

                                  <table style="width: 100%;border-right: none !important;" class="inner_tabel">

                                  <?php $i=1; ?>
                                  @if(isset($fromOrders->getAssociateOrderForBill->getAssociateItemWithOrder) && count($fromOrders->getAssociateOrderForBill->getAssociateItemWithOrder)>0)


                                  @foreach($fromOrders->getAssociateOrderForBill->getAssociateItemWithOrder as $fitem)

                                   <tr>
                                <td width="10%">{!! $i !!}.</td>
                                <td width="10%"><input type="checkbox" name = "ordered_item_id[{!! $fitem->id !!}]"/></td>
                                <td width="30%">{!! $fitem->item_title !!}</td>

                                 <td width="50%">

                                   {!! Form::number('selected_qty_'.$fitem->id, $fitem->item_quantity,['class'=>'form-control','style'=>'width:30%;','min'=>1,'max'=>$fitem->item_quantity,'required'=>true]) !!}  

                                     {!! Form::hidden('original_qty_'.$fitem->id,$fitem->item_quantity) !!}  
                                 </td>
                              </tr>


                                  <?php $i++; ?>
                                  @endforeach
                                   @endif


                                  </table>


                                 @endif


                                </td>


                              </tr>



                              

                              @endforeach

                              @endif

                               <tr>
                                <td style="margin-top: 10px;"><br>
                                <b>Void Reason:</b>
                                {!! Form::text('void_reason',null,['class'=>'form-control','required'=>true,'maxLength'=>'255','style'=>'height:50px;','placeholder'=>'Please put void reason']) !!}  </td>
                              </tr>

                         


                              </table>
                             
                             
                              
                             
                              
                              

                              

                              </td>
                          

                            </tr>

                            <tr>
                              <td colspan="2"><button class="btn btn-success">Make Void</td></button>

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



