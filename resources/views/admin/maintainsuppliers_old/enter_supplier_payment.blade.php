
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"> <b>
                   Payment to {{ ucfirst($supplier->name) }} on {{ date('d/m/Y')}} 
                             </b></div>

         @include('message')
        <form class="validate form-horizontal" target="_blank" role="form" method="POST" action="{{ route($model.'.post-supplier-payment',$supplier->slug) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Bank Account</label>
                    <div class="col-sm-10">
                        {!!Form::select('wa_bank_account_id', getBankAccountDropdowns(), null, ['placeholder'=>'Select bank account', 'class' => 'form-control select2', 'required'=>true  ])!!} 
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Date Paid</label>
                    <div class="col-sm-10">
                        {!! Form::text('date_paid', date('Y-m-d'), ['maxlength'=>'255','placeholder' => 'Title', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true ]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Supplier Currency</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_currency_manager_id', getAssociatedCurrenyList(),$supplier->wa_currency_manager_id, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Payment Type</label>
                    <div class="col-sm-10">
                        {!!Form::select('payment_type_id', getPaymentmeList(), null, ['placeholder'=>'Select payment type', 'class' => 'form-control select2' , 'required'=>true ])!!} 
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Amount</label>
                    <div class="col-sm-10">
                        {!! Form::number('amount', null, ['min'=>'0',  'class'=>'form-control amnt','required'=>true]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body ">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
						<input type="hidden" name="reference" value="" />
						<button type="button" class="btn btn-danger reference">Invoice Allocation</button>
                    </div>
                    <div class="col-sm-7 bg bg-info calc" style="display:none;">
						<p id="diff_amount"></p>
						<p id="diff_amount_msg" style="color:red;"></p>

                    </div>
                </div>
            </div>

             <div class="box-body tblsettlement" style="display: none;">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">*</label>
                    <div class="col-sm-10">
                        <table class="table table-responsive bg bg-danger">
                            <thead class="">
                                <tr>
                                    <th>Invoice No.</th>
                                    <th>Total Amount Inc. VAT</th>
                                    <th>Allocated Amount</th>
                                    <th>Yet To Be Allocated Amount</th>
                                    <th>To Be Allocated Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($supptrans as $k=> $supptran)
                                        <tr>
                                            <td>{{$supptran->document_no}}</td>
                                            <td>{{$supptran->total_amount_inc_vat}}</td>
                                            <td>{{$supptran->allocated_amount}}</td>
                                            @php
                                             $tobeallocatedamount = $supptran->total_amount_inc_vat - $supptran->allocated_amount;
                                            @endphp
                                            <td>{{number_format((float)$tobeallocatedamount, 2, '.', '')}}</td>
                                        <td><input type="number" max="{{$tobeallocatedamount}}" min="0" name="to_be_allocated[{{$supptran->id}}]" class="form-control tobeallocate" id="allocatedamnt-{{$k}}" placeholder="To Be Allocated Amount" value="0.00"></td>
                                        </tr>
                                    @endforeach

                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
            


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Narrative</label>
                    <div class="col-sm-10">
                        {!! Form::text('narrative', null, ['maxlength'=>'255',  'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>


              
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Cheque Number</label>
                    <div class="col-sm-10">
                        {!! Form::text('cheque_number', null, ['maxlength'=>'255',  'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            
           


            
           
             


            <div class="box-footer" align="right">
                <button type="submit" class="btn btn-primary processbtn" disabled="">Process Payment</button>
            </div>
        </form>
    </div>
</section>
@endsection


@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
    $(function () {
    $('.select2').select2();
      $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
});

function format(n, sep, decimals) {
    sep = sep || "."; // Default to period as decimal separator
    decimals = decimals || 2; // Default to 2 decimals

    return n.toLocaleString().split(sep)[0]
        + sep
        + n.toFixed(decimals).split(sep)[1];
}
    $(document).ready(function(){
        $('.reference').click(function(){
            $('.tblsettlement').fadeIn('1000');
        });
        $('.tobeallocate').keyup(function(){
            var sum = 0;
            $(".tobeallocate").each(function(){
                sum += +$(this).val();
            }); 
            var enteramnt = parseInt($('.amnt').val());
            var diffamnt = parseInt(enteramnt-sum);
           // console.log(enteramnt+" === "+sum+" ::: "+diffamnt);
            $('#diff_amount').html("<h4>"+format(diffamnt)+"</h4>");
            $('.calc').show();
            if(diffamnt <= 0){
           $(".tobeallocate").each(function(key, val){
             //  console.log(key+" ==== "+$("#allocatedamnt-"+key).val());
                if($("#allocatedamnt-"+key).val()=="0.00"){
                // alert("comming 2");
                    $("#allocatedamnt-"+key).attr('disabled','disabled');
                }else{
                    $("#allocatedamnt-"+key).removeAttr('disabled');
                }
            }); 
                
            }else{
                $(".tobeallocate").removeAttr('disabled');
            }
            if(diffamnt == 0){
            $('.calc').hide();
                $('#diff_amount_msg').html('');
                $('.processbtn').removeAttr('disabled');
            }else{
                $('#diff_amount_msg').html('<b>All "To Be Allocated Amount" sum should equal to Amount.</b>');
                $('.processbtn').attr('disabled','disabled');
            }
            //alert(sum);
        });
        $('.amnt').keypress(function(){
            
			//alert("done");
        });
        $('.processbtn').click(function () { 
            setTimeout(() => {
                location.href="{{ route($model.'.index') }}"
            }, 1000);
         });
    });



</script>

@endsection




