
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
                        {!! Form::text('to', request()->get('to'), ['maxlength'=>'255','placeholder' => 'Start Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}  
                    </div>
                    <div class="col-sm-3">
                        {!! Form::text('from',request()->get('from'), ['maxlength'=>'255','placeholder' => 'End Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}  
                    </div>
                    <div class="form-group col-sm-3">
                        {!! Form::select('restaurant',$restroList, null,['placeholder'=>"Select Branch",'class'=>'form-control']) !!}  
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                       </div>
                </div>

            </div>
            <div class="box-body" style="z-index: 1; position: absolute;">
            <div class="form-group">
            {{-- <div class="col-sm-3">
                     <button type="submit" class="btn btn-primary">Filter</button>
                    </div> --}}
               </div>
               </div>
            </form>
            </div>

            <div class="col-md-12 no-padding-h  table-responsive">
                <table class="table table-bordered table-hover" id="create_datatable1">
                    <thead>
                        <tr>
                            <th width="10%">S.No.</th>
                            <th width="10%"  >Period</th>
                            <th width="10%"  >Date</th>
                            <th width="10%"  >Restaurant</th>
                            <th width="10%"  >GL Account</th>
                            <th width="10%"  >GL Account Name</th>
							 <th width="10%"  >Account No</th>

                            <th width="10%"  >Description</th>
                            <th width="10%"  >Reference</th>

                             <th width="10%"  >Transaction Type</th>
                            <th width="10%"  >Transaction No</th>
                            <th width="10%"  >Debit</th>
                             <th width="10%"  >Credit</th>
                        </tr>
                    </thead>
                    {{-- <tbody>
                        <?php $b = 1;
                       $account_codes =  getChartOfAccountsList();
                      
                         ?>
                        @foreach($data as $row)

                        <tr>
                            <td>{!! $b !!}</td>

                            <td>{!! $row->period_number !!}</td>
                            <td>{!! getDateFormatted($row->trans_date) !!}</td>
                            <td>{!! (isset($row->restaurant->name)) ? $row->restaurant->name : '----' !!}</td>
                            <td>{!! $row->account !!}</td>
                            <td>{!! isset($account_codes[$row->account]) ? $account_codes[$row->account] : '' !!}</td>
                            @if($row->transaction_type=="Sales Invoice" && $row->amount > 0)
							@php
							$accountno = explode(':',$row->narrative);
							@endphp
                            <td>{!! (count($accountno)> 1 ) ? $accountno[0] : '---' !!}</td>
                            
                            @else
							@php
							$accountno = explode('/',$row->narrative);
							@endphp
                            <td>{!! (count($accountno)> 1 ) ? $accountno[1] : '---' !!}</td>
							@endif
                            <td>{!! $row->narrative !!}</td>
                            <td>{!! $row->reference !!}</td>
                            <td>{!! $row->transaction_type !!}</td>
                            <td>{!! $row->transaction_no !!}</td>
                            <td>{!! $row->amount>='0'?$row->amount:'' !!}</td>
                            <td>{!! $row->amount<='0'?$row->amount:'' !!}</td>

                            
                        </tr>
                        <?php $b++; ?>
                        @endforeach

                    </tbody> --}}
                    <tfoot>
                    <tr> 
                        <td></td>
                        <td width=""></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="10%" class="text-right"><b>Total</b></td>
                        <td width="10%" class="text-right"><b id="positiveAMount"></b></td>
                        <td width="10%" class="text-right"><b id="negativeAMount"></b></td>
                    </tr>
                    </tfoot>
                </table>


                 <table class="table table-bordered table-hover removeborder" >

                                </table>
            </div>
        </div>
    </div>


</section>

<style type="text/css">
    .removeborder td{
border: none !important;
}
.table td {
  font-size: 13px;
}
</style>

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
        $('body').addClass('sidebar-collapse');
    $(".mlselect").select2();
});
</script>

	{{-- <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script> --}}
	<script type="text/javascript" class="init">
	


$(document).ready(function() {

    $("#create_datatable1").DataTable({
        processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('general-ledgers.gl-entries') !!}',
                    data: function(data) {
                        data.from = $("input[name='from']").val();
                        data.to = $("input[name='to']").val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'period_number',
                        name: 'period_number'
                    },
                    {
                        data: 'trans_date',
                        name: 'trans_date',
                    },
                    {
                        data: 'restaurant.name',
                        name: 'restaurant.name',
                    },
                    {
                        data: 'account',
                        name: 'account',
                    },
                    {
                        data: 'get_account_detail.account_name',
                        name: 'getAccountDetail.account_name'
                    },
                    {
                        data: 'account_no',
                        name: 'account_no',
                    },
                    {
                        data: 'narrative',
                        name: 'narrative',
                    },
                    {
                        data: 'reference',
                        name: 'reference',
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type',
                    },
                    {
                        data: 'transaction_no',
                        name: 'transaction_no',
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                    },
                    {
                        data: 'credit',
                        name: 'credit',
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#positiveAMount").text(json.positiveAMount);
                    $("#negativeAMount").text(json.negativeAMount);
                }
            });
	// $('#create_datatable1').DataTable( {
    //     pageLength: "100",
	// 	dom: 'Bfrtip',
	// 	buttons: [
	// 		{ extend: 'excelHtml5', text: '<i class="fa fa-file-excel" aria-hidden="true">', footer: true },
	// 	]
	// } );
} );



	</script>
@endsection
