
@extends('layouts.admin.admin')
@section('content')
<?php 
$logged_user_info = getLoggeduserProfile();
 ?>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Bill ID: {!! $bill->id !!} </h3></div>
          <?php 
          $total_bill = []; 
          $totalorderamnt = 0;
          ?>
          @foreach($bill->getAssociateOrdersWithBill as $single_order)
          <?php $total_bill[] = $single_order->getAssociateOrderForBill->order_final_price;
          $totalorderamnt += $single_order->getAssociateOrderForBill->order_final_price;
           ?>

          @endforeach 
 
         @include('message')
         {!! Form::model($bill, ['method' => 'PATCH','route' => ['admin.set.bills.discount.request', $bill->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Discount(%)</label>
                    <div class="col-sm-10">
                            <input type="hidden" class="bill_amount" value="{{$totalorderamnt}}"> 
                        {!! Form::number('admin_discount_in_percent', null, ['placeholder' => 'Discount', 'class'=>'form-control discount_per','min'=>0,'max'=>$logged_user_info->max_discount_percent]) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Discount Amount</label>
                    <div class="col-sm-10">
                    
                        {!! Form::number('admin_discount_in_amount', null, ['placeholder' => 'Amount', 'class'=>'form-control discount_amnt','min'=>0]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Discount Reason</label>
                    <div class="col-sm-10">
                    
                        {!! Form::text('discount_reason', null, ['placeholder' => 'Discount Reason', 'class'=>'form-control','maxlength'=>'255']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')

@endsection

@section('uniquepagescript')

<script type="text/javascript">
    $('.discount_amnt').on('keyup',function(){
//        if($(this).val()!=""){
            var discountcalc = ($('.discount_amnt').val()/$('.bill_amount').val()*100);
            $('.discount_per').val(discountcalc.toFixed(2));
            $('.discount_per').attr('readonly','readonly');            
//        }else{
 //           $('.discount_per').removeAttr('readonly');                        
 //       }
    });
    $('.discount_per').on('keyup',function(){
        //alert('discount per');
        if($(this).val()!=""){
        $('.discount_amnt').val('');
        $('.discount_amnt').attr('readonly','readonly');
        }else{
            $('.discount_amnt').removeAttr('readonly');                        
        }

    });

</script>

@endsection


