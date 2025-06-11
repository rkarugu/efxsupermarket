
@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"> <b>Receive Payment From {{ ucfirst($customer->customer_name) }} on {{ date('d/m/Y')}}</b></div>

            @include('message')
            <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.post-customer-payment-uploads',$customer->slug) }}" enctype = "multipart/form-data">
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
                        <label for="inputEmail3" class="col-sm-2 control-label"> Customer Currency</label>
                        <div class="col-sm-10">
                            {!! Form::select('wa_currency_manager_id', getAssociatedCurrenyList(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}
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
                        <label for="inputEmail3" class="col-sm-2 control-label">Narrative</label>
                        <div class="col-sm-10">
                            {!! Form::text('narrative', null, ['maxlength'=>'255',  'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Received From</label>
                        <div class="col-sm-10">
                            {!! Form::text('paid_by', null, ['maxlength'=>'100',  'class'=>'form-control','required'=>true]) !!}
                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Upload Doc</label>
                        <div class="col-sm-10">
                            {!! Form::file('upload_file', null, ['maxlength'=>'100',  'class'=>'form-control','required'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="box-footer" align="right">
                    <button type="submit" class="btn btn-primary processbtn" >Process Payment</button>
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
        });


    </script>

@endsection




