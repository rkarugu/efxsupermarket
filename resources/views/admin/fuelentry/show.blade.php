
@extends('layouts.admin.admin')
@section('content')
<?php
// echo "<pre>";
// print_r($row->toArray());die();
?>

<section class="content">
    <div class="box box-primary">
        <div class="box-header"></div>
        @include('message')
        <div class="data-description">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Vehicle</label>
                                <div class="col-md-8">
                                    <!-- <input type="text" value="{{$row->vehicle}}"> -->
                                  <p><?= isset($row->vehicle) && $row->vehicle!=''?$row->vehicle:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Fuel Entry Date</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->fuel_entry_date) && $row->fuel_entry_date!=''?$row->fuel_entry_date:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Fuel Entry Time</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->fuel_entry_time!=''?$row->fuel_entry_time:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Odometer</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->odometer!=''?$row->odometer:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Meter</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->meter!=''?$row->meter:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Gallons</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->gallons!=''?$row->gallons:'N/A'; ?></p>
                                </div>
                            </div>
                        </div> 
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Price</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->price!=''?$row->price:'N/A'; ?></p>
                                </div>
                            </div>
                        </div> 
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Total</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->total!=''?$row->total:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Fuel Economy</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->fuel_economy!=''?$row->fuel_economy:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Cost Per Meter</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->cost_per_meter!=''?$row->cost_per_meter:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">fuel_type</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->fuel_type!=''?$row->fuel_type:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Vendor Name </label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->vendor_name!=''?$row->vendor_name:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">reference</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->reference!=''?$row->reference:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Photos</label>
                                <div class="col-md-8">
                                    <?php 
                                    $image = pathinfo($row->photos);
                                    $image_url= url('fuelentry'.$image['basename']);
                                    ?>
                                <img src="{{$image_url}}" class="users_img">
                               </div>
                            </div>
                        </div>

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Documents</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->documents!=''?$row->documents:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Comments</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->comments!=''?$row->comments:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                </div>
            </div>
        </div>
    </div>
</div>
</section>
@endsection

@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
        $('.select2').select2();
        $("#selector_selects2").select2();
    });
</script>

<style type="text/css">
.cont {
  text-align: justify;
  text-justify: inter-word;;
}
</style>

@endsection