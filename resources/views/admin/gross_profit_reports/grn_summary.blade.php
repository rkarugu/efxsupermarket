
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
                {!! Form::open(['route' => 'inventory-reports.grn-summary','method'=>'post', 'id'=>'report-form']) !!}

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
                    </div>

                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success" name="manage_request" value="filter"  >Filter</button>

                            <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage_request" value="xls">
                                <i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>

                            <button title="PDF" type="submit" class="btn btn-warning" name="manage_request" value="pdf"  >
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </button>

                            <button title="Print" type="button" class="btn btn-warning" name="manage_request" value="print"  onclick="printFile()">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </button>

                            <a class="btn btn-info" href="{!! route('inventory-reports.grn-summary') !!}"  >Clear</a>
                        </div>
                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')
@if(isset($result))
    <table class="table table-responsive">
    @php 
    $base_url = URL::to('/');
    $logged_user_info = getLoggeduserProfile();
    $grand_total = 0;
    @endphp
    <tr>
        <th colspan="3" ><b>GRN Summary Report</b></th>


    </tr>
    
    <tr style="text-align: center;">
    </tr>

    <?php foreach($result as $key => $row) { ?>
        <tr style="text-align: center;">
            <th width="18%">GRN No - <?= isset($row->getRelatedGrn->first()->grn_number) ? $row->getRelatedGrn->first()->grn_number : '' ?></th>
            <th width="18%"> Date Received : <?= $row->purchase_date ?></th>
            <th width="20%"><b>Purchase Order No</b>: <?= $row->purchase_no ?></th> 
            <th width="30%" colspan="2"><b>Supplier Name</b>: <?= isset($row->getSupplier->name) ? $row->getSupplier->name : '' ?></th>
        </tr>
        
        <tr>
            <td>Item No</td>
            <td>Description</td>
            <td>UOM</td>
            <td>Qty</td>
            <td>Cost</td>
            <td>VAT</td>
            <td>Total Cost</td>
        </tr>
        <?php
        $total = 0;
        foreach($row->getRelatedGrn as $key_item => $row_item) { 
            $invoice_info = json_decode($row_item->invoice_info);
            $nett = $invoice_info->order_price*$invoice_info->qty;
            $net_price = $nett;
            if($invoice_info->discount_percent > '0'){
                $discount_amount = ($invoice_info->discount_percent * $nett) / 100;
                $nett = $nett-$discount_amount;
            }

            $vat_amount = 0;
            if($invoice_info->vat_rate > '0') {
                $vat_amount = ($invoice_info->vat_rate*$nett)/100;
            }
            $total += $nett;
            $grand_total += $nett;
        ?>
            <tr>
                <td><?= $row_item->item_code ?></td>
                <td><?= $row_item->item_description ?></td>
                <td><?= isset($invoice_info->unit) ? $invoice_info->unit : '' ?></td>
                <td><?= $row_item->qty_received ?></td>
                <td><?= $row_item->standart_cost_unit ?></td>
                <td><?= $vat_amount ?></td>
                <td><?= $nett ?></td>
            </tr>
                
        
        <?php } ?>
            <?php if(empty($pdf)) { ?>
            <tr> </tr>
            <?php } ?>
            
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td><b>Total </b></td>
                <td> </td>
                <td> <?= $total ?> </td>
            </tr>
            
    <?php } ?>
    <?php if(empty($pdf)) { ?>
        <tr> </tr>
    <?php } ?>
            
    <tr>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td><b>Grand Total </b></td>
        <td> </td>
        <td> <?= $grand_total ?> </td>
    </tr>
@endif        
</table>
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
          url: '{{route('inventory-reports.grn-summary')}}',
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


