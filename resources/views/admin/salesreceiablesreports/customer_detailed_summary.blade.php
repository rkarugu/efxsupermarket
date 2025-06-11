
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
                            {!! Form::open(['route' => 'sales-and-receivables-reports.customer-detailed-summary','method'=>'get']) !!}

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

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!!Form::select('customer_id', getCustomersList(), null, ['placeholder'=>'Select Customer', 'class' => 'form-control mlselect'  ])!!}
                            </div>
                            </div>
 
                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>
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
                                      
                                        <th width="20%"  >Order Date</th>
                                         <th width="20%"  >Time</th>
                                         <th width="20%"  >Order No</th>
                                         <th width="40%"  >Sales Man</th>
                                         <th width="30%"  >Tel No.</th>
                                         <th width="40%"  >Items</th>
                                         <th width="20%"  >Qty</th>
                                         <th width="20%"  >Price</th>
                                         <th width="20%"  >Amount</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php $total_amount = []; ?>
                                   @foreach($all_item as $datas)
	                                   @foreach($datas->getRelatedItem as $data)
	                                    <tr>     
	                                      <td>{{ $datas->order_date}}</td>                                      
	                                      <td>{{ date('H:i A',strtotime($datas->created_at))}}</td>                                      
	                                      <td>{{ $datas->id}}</td>                                      
	                                      <td>{{ ucfirst(@$datas->getRelatedSalesman->name)}}</td>                                      
	                                      <td>{{ @$datas->getRelatedSalesman->phone_number}}</td>                                      
	                                      <td>{{ $data->item_name}}</td>                                      
	                                      <td>{{ $data->quantity}}</td>                                      
	                                      <td>{{ $data->unit_price}}</td>                                      
	                                       <td>{{ manageAmountFormat(($data->unit_price * $data->quantity)) }}</td>  
	                                    </tr>
	                                    <?php $total_amount[] = ($data->unit_price * $data->quantity); ?>
	                                    @endforeach
	                                @endforeach
                                    </tbody>
                                    <tfoot style="font-weight: bold;">
                                      <td>Grand Total</td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td>{{ manageAmountFormat(array_sum($total_amount)) }}</td>

                                    </tfoot>

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



	</script>

@endsection