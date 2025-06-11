
@extends('layouts.admin.admin')

@section('content')
 

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'sales-and-receivables-reports.sales-commission-report','method'=>'get']) !!}

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
                            {!!Form::select('salesman_id', getAllsalesmanList(), null, ['placeholder'=>'Select Salesman', 'class' => 'form-control mlselect'  ])!!}
                            </div>
                            </div>
 
                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1">
	                                 <button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button>
	                             </div>
                                 <div class="col-sm-1">
	                                 <button type="submit" class="btn btn-warning" title="Excel" name="manage-request" value="xls"  >
	                                  <i class="fa fa-file-excel" aria-hidden="true"></i>
	                                 </button>
	                             </div>
                                 <div class="col-sm-1">
	                                 <button type="submit" class="btn btn-warning" title="PDF" name="manage-request" value="pdf"  >
	                                 <i class="fa fa-file-pdf"></i>
	                                 </button>
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
                                      
                                         <th width="20%"  >Salesman</th>
                                         <th width="10%"  >Sales Amount</th>
                                         <th width="15%"  >Commission Amount</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php $sales_amount = []; ?>
                                   <?php $cost_amount = []; ?>
                                   <?php $total_amount = []; ?>
	                                   @foreach($detail as $datas)
	                                    <tr>     
	                                      <td>{{ ucfirst($datas['salesman_name'])}}</td>                                      
	                                      <td>{{ manageAmountFormat(array_sum($datas['sales_amount'])) }}</td>                                      	                                      
	                                      <td>{{ manageAmountFormat($datas['commission_amount']) }}</td>                                      	                                      
	                                    </tr>
	                                    <?php $sales_amount[] = array_sum($datas['sales_amount']); ?>
	                                    <?php $cost_amount[] = $datas['commission_amount']; ?>
	                                    @endforeach
                                    </tbody>
                                    <tfoot style="font-weight: bold;">
                                      <td>Grand Total</td>
                                      <td>{{ manageAmountFormat(array_sum($sales_amount)) }}</td>
                                      <td>{{ manageAmountFormat(array_sum($cost_amount)) }}</td>

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

     	<script type="text/javascript" class="init">
	


$(document).ready(function() {
	$('#create_datatable1').DataTable( {
        pageLength: "100",
	} );
} );



	</script>

@endsection