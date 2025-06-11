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
    margin-left: 29px;
}
.dt-buttons{
width: 10%  !important;
position: relative !important;
left: 66px !important;	
}
</style>


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            <br>
                            @include('message')
            <div class = "row">
            <form>
                     <div class="box-body">
                <div class="form-group">
                    <div class="col-sm-3">
                        {!! Form::text('to',null, ['maxlength'=>'255','placeholder' => 'Start Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}  
                    </div>
                    <div class="col-sm-3">
                        {!! Form::text('from',null, ['maxlength'=>'255','placeholder' => 'End Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}  
                    </div>
                    <div class="col-sm-3">
                        <select name="slug" id="slug" class="mlselect">
                            <option value="" selected disabled>Select Bank</option>
                            @foreach ($banks as $item)
                                <option value="{{$item->slug}}" {{request()->slug == $item->slug ? 'selected' : NULL}}>{{$item->account_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
            <div class="box-body" style="z-index: 1; position: absolute;">
                <div class="form-group">

                    <div class="row">
                    <div class="col-sm-12">
                             <button type="submit" class="btn btn-primary" name="manage" value="filter">Filter</button>
                             <button type="submit" class="btn btn-primary" name="manage" value="pdf"><i class="fa fa-file-pdf"></i></button>
                             <button type="submit" class="btn btn-primary" name="manage" value="excel"><i class="fa fa-file-excel"></i></button>
                            </div>
                       </div>
                       </div>
                </div>
            </form>

                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td style="font-weight: bold;text-align:right" colspan="8">Opening Balance</td>
                                            <td style="font-weight: bold;text-align: right">{{ manageAmountFormat($getOpeningBlance)}}</td>
                                          </tr>
    
                                    <tr> 
                                        <td width="8%">Date</td>
                                        
                                        <td width="9%">Type</td>
                                        <td width="11%">Trans No</td>
                                        {{-- <td width="10%">Parent Acc</td>
                                        <td width="13%">GL Acc</td>
                                        <td width="13%">Supplier Acc</td> --}}
                                        <td width="36%">Narration</td>
                                        <td width="16%">Particulars</td>
                                        <td width="9%">Debit</td>
                                        <td width="9%">Credit</td>
                                        <td width="15%"  style="text-align: right">Running Balance</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                   
                                        <?php 
                                        $OpeningBlance = $getOpeningBlance;
                                                                                  $total_amount = [];
                                                                                  $credit_amount = [];
                                                                                  $debit_amount = [];
                                                                                 //echo "<pre>"; print_r($row); die;
                                                    $accountsss = \App\Model\WaChartsOfAccount::get();
                                        
                                                                                ?>
                                        @foreach($row as $list)
                                         
                                        <tr class="details detailsParent tddHead"  > 
                                            <td>{!! date('d/M/Y',strtotime($list->trans_date)) !!}</td>
                                          @php
                                              $acGL = $accountsss->where('account_code',$list->account)->first();
                                              $acGL1 = $accountsss->where('account_code',$list->sub_account)->first();
                                          @endphp
                                             <td>{!! isset($number_series_list[$list->type_number])?$number_series_list[$list->type_number] : '' !!}</td>
                                             <td>{!! $list->document_no !!}</td>
                                             {{-- <td>{!! $acGL1 ? $acGL1->account_name.'('.$list->account.')' : NULL !!}</td>
                                             <td>{!! @$acGL->account_name ?? NULL !!}</td> 
                                             <td>{!! @$list->supplier_account ?? NULL !!}</td>  --}}
                                            <td>{!! $list->narration !!}</td>

                                             <td>{!! $list->reference !!}</td>
                                             <td>{!! $list->amount > 0 ? @manageAmountFormat($list->amount) : '-' !!}</td>
                                             <td>{!! $list->amount < 0 ? @manageAmountFormat(abs($list->amount)) : '-' !!}</td>
                                             <td style="text-align: right">{!! @manageAmountFormat($list->amount+$OpeningBlance) !!}</td>
                                             
                                         <?php 
                                             $total_amount[] = $list->amount;
                                             $credit_amount[] = $list->amount < 0 ? $list->amount : 0;
                                             $debit_amount[] = $list->amount > 0 ? $list->amount : 0;
                                             $OpeningBlance += $list->amount
                                         ?>
                                            
                                        
                                         </tr>
                                        @endforeach
                                  


                                    </tbody>
                                    <tfoot>
                                      <tr>
                                        <td style="font-weight: bold;text-align:right" colspan="5"></td>
                                        <td style="font-weight: bold;text-align:right;border-top: 1px solid #000;border-bottom: 1px solid #000;" colspan="1">B/F : {{ manageAmountFormat($getOpeningBlance) }}</td>
                                        <td style="font-weight: bold;text-align: right;border-top: 1px solid #000;border-bottom: 1px solid #000;">{{ manageAmountFormat(array_sum($debit_amount))}}</td>
                                        <td style="font-weight: bold;text-align: right;border-top: 1px solid #000;border-bottom: 1px solid #000;">{{ manageAmountFormat(array_sum($credit_amount))}}</td>
                                        <td style="font-weight: bold;text-align: right;border-top: 1px solid #000;border-bottom: 1px solid #000;">{{ manageAmountFormat($OpeningBlance)}}</td>
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
 <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
   
    $(".mlselect").select2();
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
			{ extend: 'excelHtml5', text: '<i class="fa fa-file-excel" aria-hidden="true">', footer: true,
                exportOptions: {
                    columns: [0,1,2,4,5,6,7,8]
                }
            
            },
		]
	} );
} );



	</script>
@endsection
