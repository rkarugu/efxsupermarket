
@extends('layouts.admin.admin')

@section('content')
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
                    <div class="form-group col-sm-3">
                        {!! Form::select('supplier_code',$supplierList, null,['placeholder'=>"Select Supplier Name",'class'=>'form-control mlselect']) !!}  
                    </div>
                </div>

            </div>
            <div class="box-body">
            <div class="form-group">
                                <div class="col-sm-1">
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                </div>

            <div class="col-sm-3">
                     <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
               </div>
               </div>
            </form>
            </div>

                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable1">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                        <th width="10%">Type</th>
                                        <th width="20%">Document No</th>
                                        <th width="10%">Date</th>
                                        <th width="20%">Refrence</th>
                                        <th width="20%">Allocated Amount</th>
                                        <th width="20%">Settled Amount</th>
                                        <th width="20%">Total</th>
                                        
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
                                                <td>{!! $list->document_no !!}</td>
                                                <td>{!! $list->trans_date !!}</td>
                                                <td>{!! $list->suppreference !!}</td>
                                                @if ($list->total_amount_inc_vat < 0)
                                                   <td>----</td>
                                                <td>----</td>
                                                @else
                                                   <td>{!! $list->allocated_amount !!}</td>
                                                <td>{!! manageAmountFormat($list->total_amount_inc_vat - $list->allocated_amount) !!}</td>
                                                @endif

                                                <td>{!! manageAmountFormat($list->total_amount_inc_vat) !!}</td>
                                              
                                            </tr>
                                           <?php $b++; 
                                           $total_balance[] = $list->total_amount_inc_vat;

                                           ?>
                                        @endforeach
                                    @endif


                                    </tbody>
 
                                    <tfoot>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="font-weight: bold;">Total</td>
                                        <td style="font-weight: bold;">{{ manageAmountFormat(array_sum($total_balance)) }}</td>
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
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>	
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<?php
$tableheading = "";

if(isset($_GET['supplier_code']) && $_GET['supplier_code']!=""){

	$tableheading = " Supplier Name : ".$supplierList[$_GET['supplier_code']];	

}
if(isset($_GET['to']) && $_GET['to']!=""){
	$tableheading .= '\n \n To : '.$_GET['to'];	

}
if(isset($_GET['from']) && $_GET['from']!=""){

	$tableheading .= '\n \n From : '.$_GET['from'];	

}
?>
	<script type="text/javascript" class="init">
	


$(document).ready(function() {
	$('#create_datatable1').DataTable( {
        pageLength: "100",
	} );
} );



	</script>
@endsection
