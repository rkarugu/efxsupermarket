
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




                                        .custom-table thead th{
                                            
                                            text-align: left!important;
                                          }

                                .table tr td{
                                    text-align:left !important

                                }
                                .ml-2 {
                                display: inline-block;
                                margin-bottom: 6px;
                                   }

                                            .ml-2.flex.items-center.first\:ml-0 {
                                                margin-right: 87px;
                                          }
                                          .top-box{
                                            display: flex !important;
                                            align-items: center !important;
                                          }
                                          .btn-div{
                                            padding-top: 10px !important;
                                            display: flex;
                                          }
                                           .btn-div button{
                                            margin-right: 10px;
                                           }


                            
                       

</style>
    
    <section class="content">
        <!-- Graph boxes  -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="col-md-12 no-padding-h table-responsive">
                    <div class="col-sm-6">
                        <span>Total Cost</span>
                        <h1 style="margin-top:0;">{{manageAmountFormat($total_service_total)}}</h1>

                        <span>Service Entry</span>
                        <h3 style="margin-top:0;">{{manageAmountFormat($total_service_entry)}}</h3>

                        <span>Service Parts</span>
                        <h3 style="margin-top:0;">{{manageAmountFormat($total_service_parts)}}</h3>
                        
                        <span>Service Labor</span>
                        <h3 style="margin-top:0;">{{manageAmountFormat($total_service_labor)}}</h3>

                        <span>Service Task </span>
                        <h3 style="margin-top:0;">{{manageAmountFormat($total_service_task)}}</h3>
                    </div>
                    
                    <div class="col-sm-6">
                        <span>Cost Breakdown</span>
                        <h2>Graph Here</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>                    


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            @include('message')
                   
                            <div class="col-md-12 no-padding-h table-responsive">
                                <form  method="get">
                                     
                                        
                                        <div class="row top-box">                              
                                            <div class="col-md-3 form-group">
                                                <label for="">Date From</label>
                                                <input type="date" value="{{ isset(request()->from)?request()->from:date('Y-m-01') }}" name="from" id="from" class="form-control">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label for="">Date to</label>
                                                <input type="date" value="{{isset(request()->to)?request()->to:date('Y-m-d')}}" name="to" id="to" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <div class="btn-div">
                                                    <input type="submit" id="filter" name="manage" value="Filter" class="btn btn-primary" />
                                                    &nbsp;
                                                    <input type="submit" id="filter" name="manage" value="PDF" class="btn btn-primary">

                                                    <!-- <div><a class="btn"  href="{{route('exportpdfoperatingassigemet')}}"><i class="fa fa-file-pdf" aria-hidden="true" style="font-size:24px; color:#ff0000 ;"></i></a></div> -->
                                                </div>
                                            </div>

                                        </div>
                                </form>
                                   
                                

                                <table class="table table-bordered table-hover" id="create_datatable1">
                                    <thead>
                                        <tr>
                                            <th width="5%">S.No.</th>
                                            <th>Vehicle</th>
                                            <th>Last Service Date</th>
                                            <th>Service Entries</th>
                                            <th>Service Tasks</th>
                                            <th>Parts</th>
                                            <th>Labour</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lists as $key => $list)
                                            <tr>
                                                <td>{!! ++$key !!}</td>
                                                <td>{!! $list->license_plate !!}</td>
                                                <td style="text-align:right !important">{{ $list->last_service_date}}</td>
                                                <td style="text-align:right !important">{{ manageAmountFormat($list->vehicle_service_entry)}}</td>
                                                <td style="text-align:right !important">{{ manageAmountFormat($list->vehicle_service_task)}}</td>
                                                <td style="text-align:right !important">{{ manageAmountFormat($list->vehicle_service_parts)}}</td>
                                                <td style="text-align:right !important">{{ manageAmountFormat($list->vehicle_service_labor)}}</td>
                                                <td style="text-align:right !important"> {{ manageAmountFormat($list->vehicle_service_total) }} </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfooter>
                                        <tr>
                                            <th class="text-right" colspan="3">Total :</th>
                                            <th class="text-right">{{manageAmountFormat($total_service_entry)}}</th>
                                            <th class="text-right">{{manageAmountFormat($total_service_task)}}</th>
                                            <th class="text-right">{{manageAmountFormat($total_service_parts)}}</th>
                                            <th class="text-right">{{manageAmountFormat($total_service_labor)}}</th>
                                            <th class="text-right">{{manageAmountFormat($total_service_total)}}</th>
                                        </tr>
                                    </tfooter>
                                </table>

                               
                                
                            </div>
                        </div>
                    </div>


    </section>
@endsection
@section('uniquepagescript')
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
	<script type="text/javascript" class="init">
	
function type_supplier_amount(params) {
    var url = $(params).attr('href');
    var ty = $('#type_supplier_amount').val();
    url = url+'&type='+ty;
    location.href=url;
}

$(document).ready(function() {
	$('#create_datatable1').DataTable( {
        pageLength: "100",
		dom: 'frtip',
	} );
    // $('#create_datatable2').DataTable( {
    //     pageLength: "100",
	// 	dom: 'B',
	// 	buttons: [
	// 		{ extend: 'excelHtml5', text: '<i class="fa fa-file-excel" aria-hidden="true">', footer: true },
	// 	]
	// } );
} );
            </script>

            <script type="text/javascript">
                    var dataTable = $('#dataTable').DataTable({
                        processing: true,
                        serverSide: true,
                        order: [[0, "desc" ]],
                        pageLength: '<?= Config::get('params.list_limit_admin')?>',
                        "ajax":{
                        'url':'{!! route('operatingcostsummary.index') !!}',
                        "dataType": "json",
                        "type": "GET",
                         'data': function(data){
          // Read values
            data.processed=$('#is_processed').val();
            var from = $('#from').val();
            var to = $('#to').val();

            data.from = from;
            data.to = to;
        }

    }
});




            </script>




@endsection 