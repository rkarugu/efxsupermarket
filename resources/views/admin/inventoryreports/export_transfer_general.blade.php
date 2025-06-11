
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
                {!! Form::open(['route' => 'inventory-reports.export-transfer-general','method'=>'post', 'id'=>'report-form']) !!}

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

                            <a class="btn btn-info" href="{!! route('inventory-reports.export-transfer-general') !!}"  >Clear</a>
                        </div>
                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')
            @if(isset($result))
            <div class="col-sm-12">
<table class="table table-responsive">
    <tr>
        <td  colspan="3" ><b>{{@$restuarantname}}</b></td>
      
    </tr>
    
    <tr>
        <td><b>Transfer Journal</b></td>
        <td> <b>From:</b></td> 
        <td> {{ isset($start_date)?$start_date:'-' }}</td>
        <td> <b>To:</b></td> 
        <td> {{ isset($end_date)?$end_date:'-' }}</td>
    </tr>


    <tr>
        <td><b>Article</b></td>
        <td><b>Quantity</b></td> 
        <td><b>Unit</b></td>
        <td><b>Ave Price</b></td>
        <td><b>Line Total</b></td>
    </tr>
    <?php 
$base_url = URL::to('/');
$logged_user_info = getLoggeduserProfile();
$grand_total = 0;
foreach ($result as $key => $row){ ?>
        <tr>
            <td><?= $row->transfer_no ?></td>
            <td><?= $row->transfer_date ?></td>
            <td><b> From </b></td>
            <td><?= isset($row->fromStoreDetail->location_name) ? $row->fromStoreDetail->location_name : '' ?></td>
            <td><b> To </b></td>
            <td><?= isset($row->toStoreDetail->location_name) ? $row->toStoreDetail->location_name : '' ?></td>
        </tr>
        <?php 
            $items = $row->getRelatedItem;
            $total_cost_sum = 0;
            foreach($items as $item_key => $item_row) {
                $total_cost = isset($item_row->total_cost) ? $item_row->total_cost : 0;
                $total_cost_sum += $total_cost;
                $grand_total += $total_cost;
            ?>
        <tr>
            <td> <?= isset($item_row->getInventoryItemDetail->title) ? $item_row->getInventoryItemDetail->title . ' ('. $item_row->getInventoryItemDetail->stock_id_code .')' : '' ?></td>
            <td> <?= isset($item_row->quantity) ? $item_row->quantity : '' ?></td>
            <td> <?= isset($item_row->getInventoryItemDetail->getUnitOfMeausureDetail->title) ? $item_row->getInventoryItemDetail->getUnitOfMeausureDetail->title : '' ?></td>
            <td> <?= isset($item_row->standard_cost) ? number_format((float)$item_row->standard_cost, 2, '.', '') : '' ?></td>
            <td style="text-align: right;"> <?= $total_cost ?></td>
        </tr>
        
        <?php } ?>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td><b> Total: </b></td>
            <td style="text-align: right;"><b> <?= number_format((float)$total_cost_sum, 2, '.', '') ?> </b></td>
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
            <td style="text-align: right;"><b> <?= number_format((float)$grand_total, 2, '.', '') ?> </b></td>
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
          url: '{{route('inventory-reports.export-transfer-general')}}',
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


