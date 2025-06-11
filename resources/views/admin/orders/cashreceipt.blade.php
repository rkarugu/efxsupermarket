
@extends('layouts.admin.admin')
@section('content')

<style type="text/css">
    .increasefontsize{
        font-size: 16px;
    }
</style>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">  </h3></div>
         @include('message')
          {!! Form::model($bill, ['method' => 'PATCH','route' => ['admin.post.cash.receipt', $bill->slug],'class'=>'validate','enctype'=>'multipart/form-data','id'=>'billreceiptcall' ]) !!}
            {{ csrf_field() }} 

            <input type="hidden" name="print_receipt_flag" id="print_receipt_flag" value="no">

                        <div class="box-body">
                <div class="form-group">

                <label for="inputEmail3" class="col-sm-2 control-label">Tender Amount:</label>
                 <div class="col-sm-2" >
                 <input type="text" id="tender_amount"  class="form-control digitsonly"/>
                 </div>

                   <label for="inputEmail3" class="col-sm-2 control-label increasefontsize">Change Amount: <span id="change_amount">0</span></label>


                    <label for="inputEmail3" style="font-weight: bold;" class="col-sm-2 control-label">Bill No:   {!! $bill->id!!}</label>
                   

                    <label for="inputEmail3" class="col-sm-2 control-label increasefontsize">Amount: <?php 
                    $total_bill = []; ?>
                    @foreach($bill->getAssociateOrdersWithBill as $single_order)
                    <?php $total_bill[] = round($single_order->getAssociateOrderForBill->order_final_price);
                    ?>
                    @endforeach
                        {!! Form::hidden('total_amount', array_sum($total_bill), ['id'=>'total_unbilled_amount']) !!} 
                        {!! manageAmountFormat(array_sum($total_bill)) !!} </label>
                    <label for="inputEmail3" class="col-sm-2 control-label increasefontsize">Due Amount:  <span id ="due_amount1">{!! manageAmountFormat(array_sum($total_bill)) !!} </span></label>
                  
                  
                 

                   

                    


                   
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                   
                    <div class="col-sm-2">

                     <button type="submit" class="btn btn-primary close_the_bill">Update</button>
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
                    

                    <input type= "text" name = "billing_info[{!!$keys!!}][narration]" id = "billing_narration_{!! $keys !!}" class= "billing_narration form-control" >
                     </div>
                </div>
            </div>
            <?php 
            $pc++ ;
            ?>
            @endforeach

             <div class="box-body">
                <div class="form-group increasefontsize">
                    
                     <label for="inputEmail3" class="col-sm-3 control-label">Due Amount</label>
                     
                     <div class="col-sm-9 ">
                    

                    <span id ="due_amount">{!! manageAmountFormat(array_sum($total_bill)) !!} </span>
                     </div>

                   
                </div>
            </div>

            
            
            
             
             


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


  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Do you want print receipt ?</h4>
        </div>
        <div class="modal-body">
                       <div class="box-body" align="center">
                <div class="form-group">

                     <div class="col-sm-4">

                     
                    </div>
                   
                   
                    <div class="col-sm-2">

                     <button class="btn btn-success printreceiptbutton" data="yes">Yes</button>
                    </div>

                     <div class="col-sm-2">
                     <button class="btn btn-success printreceiptbutton" data="no">No</button>
                     
                    </div>
                      <div class="col-sm-4">

                     
                    </div>

                    


                   
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
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
            $("#due_amount").html(numberWithCommas(parseFloat(due_amount)));
             $("#due_amount1").html(numberWithCommas(parseFloat(due_amount)));
         });

          $("#tender_amount").keyup(function (event) {

            var amount = $(this).val();
           
            if(amount != "")
            {
               var unbilled_amount_total = $("#total_unbilled_amount").val();
            
               //var billed_amount_total = gettotalamount();
          //  var unbilled_amount_total = $("#total_unbilled_amount").val();
            var change_amount = parseFloat(amount)-parseFloat(unbilled_amount_total);
           // $("#due_amount").html(parseFloat(due_amount));
             $("#change_amount").html(numberWithCommas(parseFloat(change_amount)));
            }
           
         });


         

         $(".close_the_bill").click(function(){
            var billed_amount_total = gettotalamount();
            var unbilled_amount_total = $("#total_unbilled_amount").val();
            var due_amount = parseFloat(parseFloat(unbilled_amount_total)-parseFloat(billed_amount_total));
            if(due_amount == 0)
            {
                
                $('#myModal').modal('show');
                return false;
            }
            else
            {
                alert('Payment amount is not matched with unblled amount');
                return false;
            }


         });

         function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

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

         $(".printreceiptbutton").click(function(){
            var print_receipt_flag = $(this).attr('data');
            $("#print_receipt_flag").val(print_receipt_flag);
            $("#billreceiptcall").submit()

         })


        

        </script>
@endsection


