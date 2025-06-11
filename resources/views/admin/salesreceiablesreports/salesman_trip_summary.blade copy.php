
@extends('layouts.admin.admin')

@section('content')
<style>
.buttons-pdf{
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
left: 80px !important;
top: -70px;
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
                            {!! Form::open(['route' => 'sales-and-receivables-reports.salesman-trip-summary','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">
  
                            <div class="col-sm-3">
                            <div class="form-group">
                            {!!Form::select('salesman_id', getAllsalesmanLists(), null, ['placeholder'=>'Select Salesman', 'class' => 'form-control mlselect getshiftdata'  ])!!}
                            </div>
                            </div>
 
                            <div class="col-sm-5">
                            <div class="form-group">
                            {!!Form::select('shift_id[]', getAllShiftLists(), null, ['placeholder'=>'Select Shift', 'class' => 'form-control  mlselec6t shiftList', 'multiple'=>'multiple'  ])!!}
                            </div>
                            </div> 

                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>
                                 <div class="col-sm-1"><button type="submit" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf"></i></button></div>
                            </div>
                            </div>

                            </form>
                        </div>
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable1" width="100%">
                                    <thead>
                                    <tr>
                                      
                                        <th width="20%"  >Item Code</th>
                                         <th width="20%"  >Item Taken</th>
                                         <th width="20%"  >Item Returned</th>
                                         <th width="20%"  >Item Sold</th>
                                         <th width="30%"  >Average Price</th>
                                         <th width="40%"  >Standard Cost</th>                                        
                                         <th width="40%"  >Total Sale</th>                                        
                                         <th width="40%"  >Total Cost</th>                                        
                                         <th width="40%"  >Margin</th>                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php 
	                                   $total_amount = []; 
	                                   
                                     $total_margin = []; 

	                                   $itemtaken = [];
	                                   $itemreturn = [];
	                                   $itemsold = [];
                                   ?>
 	                                   @if($all_item)
 	                                   @foreach($all_item as $key=> $datas)
	                                    <tr style="background: #eee;">     
	                                      <th colspan="9">{{ $key}}</th>                                      
	                                    </tr>
	 	                                   @foreach($datas as $data)
	                                    <tr>     
	                                      <td>{{ $data->stock_id_code}}</td>                                      
	                                      <td>{{ $data->item_taken }}</td>                                      
	                                      <td>{{ $data->item_returned }}</td>                                      
	                                      <td>{{ abs($data->item_sold) }}</td>                                      
	                                      <td>{{ manageAmountFormat($data->avg_price)}}</td>                                      
	                                      <td>{{ manageAmountFormat($data->standard_cost)}}</td>                                      
 	                                       <td>{{ manageAmountFormat(abs(($data->item_sold * $data->avg_price))) }}</td>  
 	                                       <td>{{ manageAmountFormat(abs(($data->item_sold * $data->standard_cost))) }}</td>  
 	                                       <td>{{ manageAmountFormat(abs($data->item_sold * $data->avg_price) - abs($data->item_sold * $data->standard_cost)) }}</td>  
	                                    </tr>
	                                    <?php 
		                                    $total_amount[] = ($data->item_sold * $data->avg_price); 

		                                    $total_margin[] = abs($data->item_sold * $data->avg_price) - abs($data->item_sold * $data->standard_cost); 

                                        $itemtaken[] = $data->item_taken; 
		                                    $itemreturn[] = $data->item_returned; 
		                                    $itemsold[] = $data->item_sold; 
		                                    ?>
											@endforeach
	                                    @endforeach
                                    </tbody>
                                    <tfoot style="font-weight: bold;">
                                      <td>Grand Total</td>
                                      <td>{{  (abs(array_sum($itemtaken))) }}</td>
                                      <td>{{  (abs(array_sum($itemreturn))) }}</td>
                                      <td>{{  (abs(array_sum($itemsold))) }}</td>
                                      <td></td>
                                      <td>{{ manageAmountFormat(abs(array_sum($total_amount))) }}</td>
                                      <td></td>
                                      <td></td>
                                      <td>{{ manageAmountFormat(abs(array_sum($total_margin))) }}</td>

                                    </tfoot>

	                                    @endif
                                </table>
                            </div>

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
<script type="text/javascript">
    $(function () {
   
    $(".mlselect").select2();
});
</script>

<script>
$(document).ready(function(){
  $(".getshiftdata").change(function(){
	  var salesmanId = $(this).val();
    $.ajax({
	    url: "{{route('sales-and-receivables-reports.getShiftBySalesman')}}",
	    dataType:"JSON", 
	    data:{'_token':"{{csrf_token()}}",salesman_id:salesmanId},
	    success: function(result){
		    $('.shiftList').html('');
			$.each(result, function (key, val) {
		    $('.shiftList').append('<option value="'+key+'">'+val+'</option>');
			});
//			$("#div1").html(result);
    	}});
  });
});

    $(function () {
        $(".mlselec6t").select2();
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
	$('#create_datatable1').DataTable( {
        pageLength: "100",
  	} );
} );



	</script>

@endsection