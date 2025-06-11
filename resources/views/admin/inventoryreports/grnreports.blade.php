
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
                {!! Form::open(['route' => 'inventory-reports.grn-reports','method'=>'get']) !!}
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
                                {!!Form::select('restaurant_id', getBranchesDropdown(),NULL, ['class' => 'form-control ','placeholder' => 'Select Branch'])!!} 
                            </div>
                        </div>


                    </div>

                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button>
                            <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  ><i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>

                            <button title="PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  >
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </button>

                            <a class="btn btn-info" href="{!! route('inventory-reports.grn-reports') !!}"  >Clear</a>
                        </div>





                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')

            @php
            //echo "<pre>"; print_r($lists); die;
            @endphp
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover">
 <?php 
$logged_user_info = getLoggeduserProfile();
 ?>

     <tr style="text-align: left;">
        <th  colspan="8">Deliveries By Suppliers</th>
    </tr>

      <tr style="text-align: left;">
        <th  colspan="2"><b>{{$restuarantname}}</b></th>
    </tr>
 

    <tr>
    <td style="text-align: left;"><b>Article</b></td>
     <td style="text-align: center;"><b>Unit</b></td>
     <td style="text-align: center;"><b>Quantity</b></td>
     <td style="text-align: right;"><b>Net</b></td>
     <td style="text-align: right;"><b>VAT</b></td>
     <td style="text-align: right;"><b>Gross</b></td>


    </tr>
    <!-- Dynamic code start -->

     <?php 
    $main_qty = [];
    $main_vat = [];
    $main_net = [];
    $main_total = [];
    ?>


    @foreach($myData as $arr)
    <tr style="text-align: left;">
        <td colspan="6"><b>{{ $arr['supplier_name']}}</b></td>
    </tr>
    <?php 
    $sub_qty = [];
    $sub_vat = [];
    $sub_net = [];
    $sub_total = [];
    ?>
    @foreach($arr['items'] as $item)
        <tr style="text-align: right;">
    <td style="text-align: left;">{{ $item['item_description'] }}</td>
     <td style="text-align: center;">{{ $item['unit'] }}</td>
     <td style="text-align: center;">{{ $item['quantity'] }}</td>
     <td>{{ manageAmountFormat($item['nett']) }}</td>
     <td>{{ manageAmountFormat($item['vat_amount']) }}</td>
     <td>{{ manageAmountFormat($item['total_amount']) }}</td>

     <?php 
     $sub_qty[] = $item['quantity'];
     $main_qty[] = $item['quantity'];



     $sub_vat[] = $item['vat_amount'];
     $main_vat[] = $item['vat_amount'];

     $sub_net[] = $item['nett'];
     $main_net[] = $item['nett'];

     $sub_total[] = $item['total_amount'];
     $main_total[] = $item['total_amount'];

     ?>


    </tr>
    @endforeach

            <tr style="text-align: right; border-top:2px solid #eee; border-bottom:2px solid #eee;">
    <td colspan="2" style="text-align: left;"><b>Total for: {{ $arr['supplier_name']}}</b></td>
  
     <td><b></b></td>
     <td><b>{{ manageAmountFormat(array_sum($sub_net)) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($sub_vat)) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($sub_total)) }}</b></td>


    </tr>
    @endforeach


     <tr style="text-align: right;">
    <td colspan="2" style="text-align: left;"><b>Report Total: </b></td>
  
     <td><b></b></td>
     <td><b>{{ manageAmountFormat(array_sum($main_net)) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($main_vat)) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($main_total)) }}</b></td>


    </tr>


    
                </table>
            </div>

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


