
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
                {!! Form::open(['route' => 'inventory-reports.export-internal-requisitions','method'=>'post', 'id'=>'report-form']) !!}

                <div>
                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('start_date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('end_date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                        </div>
                        
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!!Form::select('restaurant_id', getBranchesDropdown(),NULL, ['class' => 'form-control ','placeholder' => 'Select Branch'])!!} 
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success" name="manage_request" value="filter"  >Filter</button>
                            <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage_request" value="xls"  >
                                <i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>

                            <button title="PDF" type="submit" class="btn btn-warning" name="manage_request" value="pdf"  >
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </button>
                            <a class="btn btn-info" href="{!! route('inventory-reports.export-internal-requisitions') !!}"  >Clear</a>
                        </div>
                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')
@if(isset($data_formatted))
<div class="col-sm-12">
    <?php
$base_url = URL::to('/');
$logged_user_info = getLoggeduserProfile();
$grand_total = 0;
?>
<table class="table table-responsive" style="border:2px solid #ddd;">
<?php 

?>
    <tr>
        <th colspan="5"><b> {{$restuarantname}}</b></th>
        
    </tr>
    
    <tr>
        <th colspan="2"><b>Transfers by Store / Item Group Detailed</b></th>
    </tr>


    <tr style="border:2px solid #ddd;">
        <th><b>Article</b></th>
        <th><b>Unit</b></th>
        <th><b>Quantity</b></th> 
        <th><b>Price</b></th>
        <th><b>Total</b></th>
    </tr>
    <?php foreach ($data_formatted as $key => $row){
        $key_ex = explode('-', $key);
        list($from_location_id, $to_location_id) = $key_ex;
        $from_location_row = getlocationRowById($from_location_id);
        $to_location_row = getlocationRowById($to_location_id);
        ?>
        <tr>
            <th><b> From Store: </b></th>
            <th colspan="2"><b><?= isset($from_location_row->location_name) ? $from_location_row->location_name : '' ?></b></th>
        </tr>
        <tr>
            <th><b> To Store: </b></th>
            <th colspan="2"><b><?= isset( $to_location_row->location_name) ? $to_location_row->location_name : '' ?></b></th>
        </tr>
        <?php 
            //$items = $row->getRelatedItem;
            $total_cost_sum = 0;
            foreach($row as $key_item => $item_rows){
                $quantity_total = $price_total = 0;
                foreach($item_rows as $item_key => $item_row) {
                    $quantity_total += isset($item_row->quantity) ? $item_row->quantity : 0;
                    $price_total += isset($item_row->total_cost) ? $item_row->total_cost : 0;
                    
                }
                $total_cost_sum += $price_total;
                $grand_total  += $price_total;
                ?>
                <tr>
                    <td> <?= isset($item_row->getInventoryItemDetail->title) ? $item_row->getInventoryItemDetail->title . ' ('. $item_row->getInventoryItemDetail->stock_id_code .')' : '' ?></td>

                    <td> <?= isset($item_row->getInventoryItemDetail->getUnitOfMeausureDetail->title) ? $item_row->getInventoryItemDetail->getUnitOfMeausureDetail->title : '' ?></td>

                    <td> <?= $quantity_total ?></td>
                    
                    <td style="text-align: right;"> <?= isset($item_row->standard_cost) ? manageAmountFormat($item_row->standard_cost) : '' ?></td>
                    <td style="text-align: right;"> <?= manageAmountFormat($price_total) ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td><b> Total: </b></td>
                <td style="text-align: right;"><b> <?= manageAmountFormat($total_cost_sum) ?> </b></td>
            </tr>
    <?php } ?>
        <tr>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td><b>Grand Total: </b></td>
            <td style="text-align: right;"><b> <?= manageAmountFormat($grand_total) ?> </b></td>
        </tr>
</table>
</div>
@endif

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

function printFile() {
    
    var isconfirmed=confirm("Do you want to print?");
    if (isconfirmed) 
    {
        var frm = $('#report-form');
        data_form = frm.serialize();
        jQuery.ajax({
          url: '{{route('inventory-reports.export-internal-requisitions')}}',
          type: 'POST',
          async:false,
          data:data_form,
          headers: {
             'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
        success: function (response) {
            var divContents = response;
            var printWindow = window.open('', '', 'width=600');
            printWindow.document.write('<html><head><title>Receipt</title>');
            printWindow.document.write('</head><body >');
            printWindow.document.write(divContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
      });
    }
}

</script>
@endsection


