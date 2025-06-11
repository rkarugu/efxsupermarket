      
@extends('layouts.admin.admin')

@section('content')
<style>
.buttons-html5{
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
position: relative;
left: 85px;
margin-top: -127px;
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
                {!! Form::open(['route' => 'inventory-reports.delivery-note-reports','method'=>'get']) !!}
                <div>
                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('start-date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('end-date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                        </div>

                    </div>

                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button>
<!--
                            <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  ><i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>
-->

                        </div>





                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')

            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable2">
 <?php 
$logged_user_info = getLoggeduserProfile();
 ?>

    <thead>
	<tr>
		<th width="10%">S.No.</th>		
		<th width="10%"  >Delivery Note No.</th>
		<th width="10%"  >Route</th>
		<th width="10%"  >Salesman Name</th>
		<th width="10%"  >Transfer Date</th>
		<th width="10%"  >Shift Id</th>
    </tr>
    </thead>
    <tbody>
     <?php 
    $main_qty = [];
    $main_vat = [];
    $main_net = [];
    $main_total = [];
    //echo "<pre>"; print_r($list); die;
    ?>


    @foreach($list as $key=> $val)
    <tr>
        <td>{!! $key !!}</td>
        <td>{!! ucfirst(@$val->requisition_no) !!}</td>
        <td>{!! ucfirst(@$val->route) !!}</td>
        <td>{!! isset($val->getRelatedToLocationAndStore->location_name) ? ucfirst($val->getRelatedToLocationAndStore->location_name) : '' !!}</td>
        <td>{!! ucfirst(@$val->requisition_date) !!}</td>
        <td>{!! ucfirst(@$val->getShiftInfo->id) !!}</td>
    </tr>
    @endforeach
    </tbody>
 

    
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(function () {
		$(".mlselect").select2();
    });
$('.datepicker').datepicker({
format: 'yyyy-mm-dd'
});


</script>

	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
	<script type="text/javascript" class="init">
	


$(document).ready(function() {
	$('#create_datatable2').DataTable( {
        pageLength: "100",
		dom: 'Bfrtip',
		buttons: [
			{ extend: 'excelHtml5', text: '<i class="fa fa-file-excel" aria-hidden="true">' },
			{
            extend: 'pdf',
            text: '<i class="fa fa-file-pdf" aria-hidden="true">',
            exportOptions: {
                modifier: {
                    page: 'current'
                },
			customize : function(doc) {
				doc.content[1].table.widths = ["5%", "17%", "17%", "17%", "27%", "27%"];
			}
            }
        }
		]
	} );
} );
	</script>
@endsection


