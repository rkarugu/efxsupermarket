@extends('layouts.admin.admin')
@section('content')

<?php
$action_options_arr = [
    1=>'Report The Inverntory Comparison Difference Only - No Adjustment',
    // 2=>'Report AND Close The Inverntory Comparison Processing Adjustment As Necessary',
];
?>

<section class="content">   

    <div class="row">
        <div class="col-md-12">
            <!-- Bar chart -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-bar-chart"></i>

                    <h3 class="box-title">Inventory comparison report</h3>

                    
                </div>
                <div class="box-body">
                    {!! Form::open(['route' => 'admin.stock-counts.compare-counts-vs-stock-check-update','class'=>'validate form-horizontal']) !!}
                   
                    <div class="row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Choose Option</label>
                        <div class="col-sm-4">
                            {!! Form::select('action_type', $action_options_arr,null, ['placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control mlselec6t']) !!}  
                        </div>

                         <div class="col-sm-4">
                            {!! Form::select('wa_location_and_store_id', $locationAndStores,null, ['required'=>true, 'class'=>'form-control mlselec6t']) !!}  
                        </div>
                    </div>


                    
                    
                    <div class="modal-footer">
                        <?= Form::submit('Submit', ['class'=>'btn btn-primary']); ?>
                    </div>
                    {!! Form::close() !!}
                </div>
                
                
                <!-- /.box-body-->
            </div>
            <!-- /.box -->

            <!-- Donut chart -->

            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</section>



@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
       $(function () {
            $(".mlselec6t").select2();
        });
    </script>
@endsection


