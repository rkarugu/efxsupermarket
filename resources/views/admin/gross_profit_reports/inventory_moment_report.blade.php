
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
                {!! Form::open(['route' => 'inventory-reports.inventory-moment-reports','method'=>'get']) !!}
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
                                {!!Form::select('location_id', getStoreLocationDropdown(),NULL, ['class' => 'mlselect form-control ','placeholder' => 'Select Store And Location'])!!} 
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('stock_code', null, [
                                'class'=>'form-control',
                                'placeholder'=>'Stock Code']) !!}
                            </div>
                        </div>


                    </div>

                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button>
                            <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  ><i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>

                            <a class="btn btn-info" href="{!! route('inventory-reports.grn-reports') !!}"  >Clear</a>
                        </div>





                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')

            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover">
 <?php 
$logged_user_info = getLoggeduserProfile();
 ?>

	<tr>
		<th width="10%">Txn No</th>		
		<th width="10%"  >User Name</th>
		<th width="10%"  >Stock ID</th>
		<th width="10%"  >Store Location</th>
		<th width="10%"  >Quantity</th>
		<th width="10%"  >Price</th>
		<th width="10%"  >Refrence</th>
		<th width="10%"  >Total Amount</th>
		<th width="10%"  >Document No</th>
		<th width="10%"  >Type</th>
		<th width="10%"  >Created At</th>
    </tr>
     <?php 
    $main_qty = [];
    $main_vat = [];
    $main_net = [];
    $main_total = [];
    //echo "<pre>"; print_r($list); die;
    ?>


    @foreach($list as $val)
    <tr>
        <td>{!! manageOrderidWithPad(@$val->id) !!}</td>
        <td>{!! ucfirst(@$val->getRelatedUser->name) !!}</td>
        <td>{!! ucfirst(@$val->stock_id_code) !!}</td>
        <td>{!! isset($val->getLocationOfStore->location_name) ? ucfirst($val->getLocationOfStore->location_name) : '' !!}</td>
        <td>{!! @$val->qauntity !!}</td>
        <td>{!! @$val->price !!}</td>
        <td>{!! @$val->refrence !!}</td>
        <td>{!! manageAmountFormat($val->price*$val->qauntity) !!}</td>
         <td>{!! @$val->document_no !!}</td>
        <td>{!! getStockMoveType($val) !!}</td>
        <td>{!! @$val->created_at !!}</td>
    </tr>
    @endforeach


 

    
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(function () {
		$(".mlselect").select2();
    });
$('.datepicker').datepicker({
format: 'yyyy-mm-dd'
});


</script>
@endsection


