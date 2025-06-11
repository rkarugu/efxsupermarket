
@extends('layouts.admin.admin')
@section('content')
<?php
// echo "<pre>";
// print_r($row->Issues->issues);die();
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
                                 
                                         {{$row->LicensePlate->license_plate}}
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Odometer</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->odometer) && $row->odometer!=''?$row->odometer:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Start Date</label>
                                <div class="col-md-8">
                                    <p><?= isset($row->start_date) && $row->start_date!=''?$row->start_date:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                       
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label"> Completion Date </label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->vendor_name!=''?$row->vendor_name:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Parts</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->parts!=''?$row->parts:'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Labor</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->labor!=''?$row->labor:'N/A'; ?></p>
                                </div>
                            </div>
                        </div> 
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Subtotal</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->subtotal!=''?$row->subtotal:'N/A'; ?></p>
                                </div>
                            </div>
                        </div> 
                        

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Discount(%)</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->discount!=''?$row->discount.'%':'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Tax(%)</label>
                                <div class="col-md-8">
                                    <p><?= isset($row) && $row->tax!=''?$row->tax.'%':'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 control-label">Total</label>
                                <div class="col-md-8">
                                    <p><b><?= isset($row) && $row->total!=''?$row->total:'N/A'; ?></b></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                        <h3><b>Issues<b></h3>
                        </div>

                        <table class="table table-bordered table-hover"  >
                            <style>
                                .table tr td{
                                    text-align:left !important
                                }
                            </style>
                                    <tbody>
                                                   <?php 
                                        foreach ($row->Issues as $Issues) { ?>
                                         <tr>
                                        <td>

                                        {{ $Issues->issues}}
                                    </td>

                                      
                                         </tr>
                                         <?php }
                                    ?>
                                    </tbody>
                              </table> 



                        <div class="col-sm-12">
                        <h3><b>Service Task<b></h3>
                        </div>

                        <table class="table table-bordered table-hover"  >
                            <style>
                                .table tr td{
                                    text-align:left !important
                                }
                            </style>
                                    <thead>
                                    <tr>
                                          <th width="20%">Task</th>
                                          <th width="20%">Parts</th>
                                          <th width="20%">Labor</th>
                                          <th width="20%">Subtotal</th>



                                    </tr>
                                    </thead> 
                                    <tbody>
                                                   <?php 
                                       foreach ($row->servicetask as $servicetask) { ?>
                                         <tr>
                                        <td>

                                        {{ @$servicetask->servicetask->name}}
                                    </td>

                                       <td>
                                        {{ $servicetask->parts}}
                                    </td>


                                       <td>
                                        {{ $servicetask->labor}}
                                    </td>

                                        <td>
                                        {{ $servicetask->subtotal}}
                                     </td>
                                         </tr>
                                         <?php }
                                    ?>
                                    </tbody>
                              </table> 



 
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