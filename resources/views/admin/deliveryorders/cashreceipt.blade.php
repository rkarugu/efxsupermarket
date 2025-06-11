
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">  </h3></div>
         @include('message')
          {!! Form::model($bill, ['method' => 'PATCH','route' => ['admin.pend-delivery-orders.post.cash.receipt', $bill->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }} 

                        <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Bill No:</label>
                    <div class="col-sm-10">
                   {!! $bill->id!!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Amount</label>
                    <div class="col-sm-10">
                    <?php 
                    $total_bill = []; ?>
                    @foreach($bill->getAssociateOrdersWithBill as $single_order)
                    <?php $total_bill[] = $single_order->getAssociateOrderForBill->order_final_price;
                    ?>
                    @endforeach
                        {!! Form::hidden('total_amount', array_sum($total_bill), [  'id'=>'total_unbilled_amount']) !!} 
                        {!! manageAmountFormat(array_sum($total_bill)) !!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-1 control-label">S.n</label>
                     <label for="inputEmail3" class="col-sm-3 control-label">Payment Mode</label>
                     <label for="inputEmail3" class="col-sm-3 control-label">Amount</label>
                     <label for="inputEmail3" class="col-sm-5 control-label">Narration</label>
                   
                </div>
            </div>

            <?php 
            $pc = 1;
            ?>
            @foreach($payment_mode as $keys=> $mode_of_payment)
             <div class="box-body">
                <div class="form-group">
                    <div class="col-sm-1 ">{!! $pc !!}</div>
                     <div class="col-sm-3 ">{!! $mode_of_payment !!}</div>
                     <div class="col-sm-3 ">

                     <input type= "text" name = "billing_info[{!!$keys!!}][amount]" id = "billing_amount_{!! $keys !!}" class= "billing_amount form-control digitsonly" placeholder="Amount" autocomplete="off">
                     


                      </div>
                    <div class="col-sm-5 ">
                    

                    <textarea type= "text" name = "billing_info[{!!$keys!!}][narration]" id = "billing_narration_{!! $keys !!}" class= "billing_narration form-control" > </textarea>
                     </div>
                </div>
            </div>
            <?php 
            $pc++ ;
            ?>
            @endforeach

             <div class="box-body">
                <div class="form-group">
                    
                     <label for="inputEmail3" class="col-sm-3 control-label">Due Amount</label>
                     
                     <div class="col-sm-9 ">
                    

                    <span id ="due_amount">{!! manageAmountFormat(array_sum($total_bill)) !!} </span>
                     </div>

                   
                </div>
            </div>

             <!--div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Payment Mode</label>
                    <div class="col-sm-10">
                   
                       

                         {!!Form::select('payment_mode',$payment_mode,  null, ['placeholder'=>'Select payment mode', 'class' => 'form-control','required'=>true ])!!} 
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Bill Narration</label>
                    <div class="col-sm-10">
                   
                        {!! Form::textarea('bill_narration', null, ['maxlength'=>'250','placeholder' => 'Comment or narration',  'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div-->
            
            
             
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary close_the_bill">Update</button>
            </div>
        </form>
    </div>
</section>

<style type="text/css">
    .bill_narration{
        height: 80px;
    }
</style>
@endsection



@section('uniquepagescript')
<script>
        $(".digitsonly").keypress(function (event) {
           
            var inputCode = event.which;
            var currentValue = $(this).val();

            if (inputCode > 0 && (inputCode < 48 || inputCode > 57)) {
                if (inputCode == 46) {
                    if (getCursorPosition(this) == 0 && currentValue.charAt(0) == '-') return false;
                    if (currentValue.match(/[.]/)) return false;
                }
                else if (inputCode == 45) {
                    if (currentValue.charAt(0) == '-') return false;
                    if (getCursorPosition(this) != 0) return false;
                }
                else if (inputCode == 8) return true;
                else return false;

            }
            else if (inputCode > 0 && (inputCode >= 48 && inputCode <= 57)) {
                if (currentValue.charAt(0) == '-' && getCursorPosition(this) == 0) return false;
            }
        });

         $(".billing_amount").keyup(function (event) {
            //alert($(this).val());
            var billed_amount_total = gettotalamount();
            var unbilled_amount_total = $("#total_unbilled_amount").val();
            var due_amount = parseFloat(unbilled_amount_total)-parseFloat(billed_amount_total);
            $("#due_amount").html(parseFloat(due_amount));
         });

         $(".close_the_bill").click(function(){
            var billed_amount_total = gettotalamount();
            var unbilled_amount_total = $("#total_unbilled_amount").val();
            var due_amount = parseFloat(parseFloat(unbilled_amount_total)-parseFloat(billed_amount_total));
            if(due_amount == 0)
            {
                

            }
            else
            {
                alert('Payment amount is not matched with unblled amount');
                return false;
            }

         });

         function gettotalamount()
         {
            var filled_amount = [];
            $( ".billing_amount" ).each(function( index ) {
                filled_amount.push(parseFloat($( this ).val()));
            });
            var total = 0;
            for (var i = 0; i < filled_amount.length; i++) {
                total+=parseFloat(filled_amount[i]) || 0;
            }
            return total;
         }

         function refreshdueamount()
         {

         }


        

        </script>
@endsection


