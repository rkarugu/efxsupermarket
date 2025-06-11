
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
                               <form action="">
                                <div>
                                <div class="col-md-12 no-padding-h">
    
    
                                <div class="col-sm-3">
                                <div class="form-group">
                                {!! Form::text('from_date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                                </div>
                                </div>
    
                                <div class="col-sm-3">
                                <div class="form-group">
                                {!! Form::text('to_date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'End Date','readonly'=>true]) !!}
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
                                        <th width="10%">S.No.</th>
                                        <th width="10%">Type</th>
                                        <th width="10%">TXN No</th>
                                        <th width="10%">Date</th>
                                        <th width="20%">Refrence</th>
                                        <th width="20%">Allocated Amount</th>
                                        <th width="20%">Settled Amount</th>                                         <th width="20%">Document No</th>
                                        <th width="20%">Total</th>
                                        <th  width="20%" class="noneedtoshort" >Action</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php 

                                    $total_balance = [];
                                    ?>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                <td>{!! isset($number_series_list[$list->grn_type_number])?$number_series_list[$list->grn_type_number] : '' !!}</td>
                                                <td>{!! manageOrderidWithPad($list->wa_purchase_order_id) !!}</td>
                                                <td>{!! $list->trans_date !!}</td>
                                                <td>{!! $list->suppreference !!}</td>
                                                @if ($list->total_amount_inc_vat < 0)
                                                   <td>----</td>
                                                <td>----</td>
                                                @else
                                                   <td>{!! $list->allocated_amount !!}</td>
                                                <td>{!! manageAmountFormat($list->total_amount_inc_vat - $list->allocated_amount) !!}</td>
                                                @endif

                                                <td>{!! $list->document_no !!}</td>
                                                <td>{!! manageAmountFormat($list->total_amount_inc_vat) !!}</td>
                                                <td class = "action_crud">
                                                    <span>
                                                        <a style="font-size: 16px;"  href="{{ route($model.'.supplier-movement-gl-entries', [($list->wa_purchase_order_id ?? $list->document_no), $supplier_code]) }}" ><i class="fa fa-list" title= "GL Entries"></i>
                                                        </a>
                                                    </span>
                                                </td>
                                            </tr>
                                           <?php $b++; 
                                           $total_balance[] = $list->total_amount_inc_vat;

                                           ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="font-weight: bold;">Total</td>
                                        <td style="font-weight: bold;">{{ manageAmountFormat(array_sum($total_balance)) }}</td>
                                        <td></td>

                                    <tfoot>
                                        

                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection


@section('uniquepagestyle')
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