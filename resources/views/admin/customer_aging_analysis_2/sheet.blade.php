
@extends('layouts.admin.admin')

@section('content')
<style>
.buttons-excel{
    background-color: #f39c12 !important;
    border-color: #e08e0b !important;
    border-radius: 3px !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: 1px solid transparent !important;
    color: #fff !important;
    display: inline-block !important;
    padding: 6px 12px !important;
    margin-bottom: 0 !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.42857143 !important;
    text-align: center !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
}
.dt-buttons{
width: 10%  !important;
position: relative !important;
left: 74px !important;
top: -45px !important;
}
</style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'customer-aging-analysis.index','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Date</label>

                                    {!! Form::text('start-date', null, [
                                    'class'=>'datepicker form-control start-date-value',
                                    'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Balance</label>
                                    <select name="type" id="type_supplier_amount" class="form-control">
                                        <option value="All" selected="">All</option>
                                        <option value="zero"> Zero Balances</option>
                                        <option value="more"> Greater OR Less than zero Balances</option>
                                    </select>
                                </div>
                            </div>
                            
{{-- 
                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('end-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                            </div> --}}
{{-- 
                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::select('restaurant',$restroList, null,['placeholder'=>"Select Branch",'class'=>'form-control']) !!}
                            </div>
                            </div> --}}
                           
                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>
                                {{-- <div class="col-sm-1">
                                <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  ><i class="fa fa-file-excel" aria-hidden="true"></i>
                                </button>
                                </div>

                                <div class="col-sm-1">
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                </div> --}}

                                <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('customer-aging-analysis.index') !!}"  >Clear</a>
                                </div>
                                
                                <div class="col-sm-3">
                                <a href="{{route('customer-aging-analysis.index',['print'=>'pdf'])}}" onclick="redirectMe(this); return false;" class="btn btn-warning">Customer Listing <i class="fa fa-file-pdf" aria-hidden="true"></i></a>
                                </div>
                                
                            </div>
                            </div>

                            </form>

                        </div>
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable1">
                                    <thead>
                                    <tr>
                                      <th></th>
                                      <th>Current</th>
                                      <th>1-30</th>
                                      <th>31-60</th>
                                      <th>61-90</th>
                                      <th> > 90</th>
                                      <th> TOTAL</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                   @php
                                   $ctotal = 0;
                                   $month1total = 0;
                                   $month2total = 0;
                                   $month3total = 0;
                                   $monthlasttotal = 0;
                                   $grandtotal = 0;
                                   @endphp   
                                  @foreach($customer as $val)
                                    <tr>
                                    <th>{{$val['customer_name']}}</th>
                                      <td>{{number_format($val['c_amount'],2)}}</td>
                                      <td>{{number_format($val['month_1_amount'],2)}}</td>
                                      <td>{{number_format($val['month_2_amount'],2)}}</td>
                                      <td>{{number_format($val['month_3_amount'],2)}}</td>
                                      <td>{{number_format($val['month_last_amount'],2)}}</td>
                                      <th>{{number_format($val['c_amount']+$val['month_1_amount']+$val['month_2_amount']+$val['month_3_amount']+$val['month_last_amount'],2)}}</th>
                                      @php
                                       $ctotal += $val['c_amount'];
                                       $month1total += $val['month_1_amount'];
                                       $month2total += $val['month_2_amount'];
                                       $month3total += $val['month_3_amount'];
                                       $monthlasttotal += $val['month_last_amount'];
                                       $grandtotal += $val['c_amount']+$val['month_1_amount']+$val['month_2_amount']+$val['month_3_amount']+$val['month_last_amount'];
                                      @endphp
                                    </tr>
                                   @endforeach
                                    </tbody>
                                 <tfoot style="background: #eee;">
                                 <tr>
                                       <th>Total</th>
                                       <th>{{number_format($ctotal,2)}}</th>
                                       <th>{{number_format($month1total,2)}}</th>
                                       <th>{{number_format($month2total,2)}}</th>
                                       <th>{{number_format($month3total,2)}}</th>
                                       <th>{{number_format($monthlasttotal,2)}}</th>
                                       <th>{{number_format($grandtotal,2)}}</th>
                                    </tr>
                                   </tfoot>
                                </table>
                            </div>

                       
                        </div>
                    </div>
    </section>


  
@endsection


@section('uniquepagestyle')
  <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script>
               

                 

                  $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
            </script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
	<script type="text/javascript" class="init">
	


$(document).ready(function() {
	$('#create_datatable1').DataTable( {
        pageLength: "100",
		dom: 'Bfrtip',
		buttons: [
			{ extend: 'excelHtml5', text: '<i class="fa fa-file-excel" aria-hidden="true">', footer: true },
		]
	} );
} );
function redirectMe(v){
    var url = $(v).attr('href');
    location.href=url+"&date="+$('.start-date-value').val()+"&type="+$('#type_supplier_amount').val();
}
            </script>

@endsection
