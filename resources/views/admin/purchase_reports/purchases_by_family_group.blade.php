
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
                {!! Form::open(['route' => 'purchases-by-family-group','method'=>'get']) !!}

                <div>
                    <div class="col-md-12 no-padding-h">
                        <div class="row">
                            <div class="col-sm-2"><label>Select period from  </label> </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::text('start-date', null, [
                                    'class'=>'datepicker form-control',
                                    'placeholder'=>'Select period from' ,'readonly'=>true, 'required'=> true]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"><label>Select period to  </label> </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::text('end-date', null, [
                                    'class'=>'datepicker form-control',
                                    'placeholder'=>'Select period to' ,'readonly'=>true, 'required'=> true]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"><label>Family Group</label> </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!!Form::select('family_group', getstockFamilyGroup(), null, ['placeholder'=>'All', 'class' => 'form-control'])!!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"><label>Show Details  </label> </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::checkbox('show_details', null); !!}  
                                </div>
                            </div>
                        </div>

                        <div class="row no-padding-h">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button>

                            <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  >
                                <i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>

                            <button title="PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  >
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </button>
                            <a class="btn btn-info" href="{!! route('purchases-by-family-group') !!}"  >Clear</a>
                        </div>
                        </div>

                        


                            
                        


                    </div>


                </div>

                </form>
            </div>

            <br>

@if(isset($data))
<div class="col-sm-12">
        <table class="table table-responsive">
 
            <tr>
                <th  colspan="3" ><b>{{$family_group_title}}</b></th>
            </tr>
 
 
            <tr style="text-align: left;">
                <th  colspan="2"><b><?= $title ?></b></th>
     
            </tr>

 


    <tr style="text-align: left;">
        <td colspan=""><b>Family Group</b></td>
        <td colspan=""><b>Total Amount</b></td>
    </tr>
    <!-- Dynamic code start -->

    <?php
    $grand_total = 0;
    ?>
    @foreach($data as $key => $row)
    <tr style="text-align: left;">
        <?php
        $famil_group_row = getFamilyGroupById($row->family_group);
        $grand_total += isset($row->total_cost_with_vat_sum) ? $row->total_cost_with_vat_sum : 0;
        ?>
        <td colspan="">
            <?= isset($famil_group_row->title) ? $famil_group_row->title : '' ?>
        </td>
        <td colspan=""><?= isset($row->total_cost_with_vat_sum) ? $row->total_cost_with_vat_sum : '' ?></td>
    </tr>
    @endforeach
  

    <tr style="text-align: left;">
        <td colspan=""><b> Grand Total </b></td>
        <td colspan=""><b>{{ manageAmountFormat($grand_total) }}</b></td>
    </tr>





        </table>
        </div>
@endif
@if(isset($detail))
<div class="col-sm-12">
<table class="table table-responsive">
     <tr>
        <th colspan="5" ><b>{{$family_group_title}}</b></th>
    </tr>
    
    <tr style="text-align: left;">
        <th colspan="4"><b>Purchases by Store Location</b></th>
    </tr>
 

    <tr style="text-align: left;">
        <td><b>Family Group</b></td>
        <td><b>Item Code</b></td>
        <td><b>Description</b></td>
        <td><b>Units</b></td>
        <td><b>Delivery Date</b></td>
        <td><b>Store</b></td>
        <td><b>PLO No</b></td>
        <td><b>QTY Received</b></td>
        <td><b>Unit Price</b></td>
        <td><b>Amount</b></td>
    </tr>
    <!-- Dynamic code start -->

    <?php
    $grand_total = 0;
    ?>
    <?php
    foreach ($detail as $key => $row_data) {
        //print_r($row_data); die;
        $sub_total = 0;
        ?>
        <tr style="text-align: left;">
            <td colspan="3"><b><?= $row_data['family_group']->title ?></b></td>
        </tr>
        <?php
        foreach ($row_data['data'] as $row_key => $order_item) {
            ?>
            <tr style="text-align: left;">
                <td>
                    <?= isset($order_item->getInventoryItemDetail->getInventoryCategoryDetail->getStockFamilyGroup->title) ? $order_item->getInventoryItemDetail->getInventoryCategoryDetail->getStockFamilyGroup->title : '' ?>
                </td>
                <td>
                    <?= isset($order_item->item_no) ? $order_item->item_no : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getInventoryItemDetail->description) ? $order_item->getInventoryItemDetail->description : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getInventoryItemDetail->getUnitOfMeausureDetail->title) ? $order_item->getInventoryItemDetail->getUnitOfMeausureDetail->title : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getPurchaseOrder->purchase_date) ? $order_item->getPurchaseOrder->purchase_date : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getPurchaseOrder->getStoreLocation->location_code) ? $order_item->getPurchaseOrder->getStoreLocation->location_code : '' ?>
                </td>
                <td>
                    <?= isset($order_item->purchase_no) ? $order_item->purchase_no : '' ?>
                </td>
                <td>
                    <?= isset($order_item->quantity) ? $order_item->quantity : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getInventoryItemDetail->standard_cost) ? $order_item->getInventoryItemDetail->standard_cost : '' ?>
                </td>
                <td>
                    <?= isset($order_item->total_cost_with_vat) ? $order_item->total_cost_with_vat : '' ?>
                </td>

            </tr>
            <?php
            $sub_total += $order_item->total_cost_with_vat;
            $grand_total += $order_item->total_cost_with_vat;
        }
        ?>
        <tr style="text-align: left;">
            <td colspan="8"> </td>
            <td>Total </td>
            <td><b>{{ manageAmountFormat($sub_total) }}</b></td>
        </tr>
    <?php } ?>

 
    <tr style="text-align: left;">
        <td colspan="8"> </td>
        <td>Grand Total </td>
        <td><b>{{ manageAmountFormat($grand_total) }}</b></td>
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
</script>
@endsection