
@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <style type="text/css">
        .date-input{
            width: 100% !important;

        }
        .same-box{
            position: relative;
        }
        .same-box input{
            position: absolute;
            top: 0;
            bottom: 0;
            margin: auto 0;
            width: ;

        }
        .input-width{
            width: 90% !important;
        }
    </style>

    


    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <h4>
                        <i class="fa fa-filter" aria-hidden="true"></i> Filter
                        <hr>
                    </h4>
                    <form method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                   <label for="">Select Tyre</label>
                                   {!!Form::select('tyre_inventory_id', getTyreList(),request()->tyre_inventory_id, ['class' => 'form-control mlselec6t tyre_list','required'=>true,'placeholder' => 'Please select','id'=>'tyre_inventory_id' ])!!} 
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                                <br>
                                <button type="submit" class="btn btn-primary subme" value="filter" name="manage">Get Tyres</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->

    @if(request()->tyre_inventory_id)
        <section class="content">
            <!-- Small boxes (Stat box) -->
                <form method="post" action="{{route('tyre_retreading.receive_retread_tyre_store')}}" class="submitMe">
                        {{csrf_field()}}
                        {{method_field('POST')}}
                        <div class="box box-primary">
                            <div class="box-header with-border no-padding-h-b">
                                <br>
                                


                                <!-- <div class="col-md-12 no-padding-h"> -->
                                <div class="col-md-12 no-padding-h table-responsive">
                                   
                                    <table class="table table-bordered table-hover" id="dataTable_stopped" style="text-align:left !important" >

                                            <!-- <table class="table table-bordered table-hover" id="dataTable"> -->
                                        <style>
                                            .table tr td{
                                                text-align:left !important
                                            }
                                        </style>
                                        <thead>
                                            <tr>
                                                <th width="5%">S.No.</th>
                                                <th>Stock Id Code</th>
                                                <th>Serial No.</th>
                                                <th>Status</th>
                                                <th>Last Odometer Reading</th>
                                                <th>Receive to Retread</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if($tyre_serials->count()>0)
                                                @foreach($tyre_serials as $key => $serial)
                                                    <tr>
                                                        <td>{{++$key}}</td>
                                                        <td>
                                                            <input type="hidden" value="{{$serial->stock_move->stock_id_code }}" name="stock_id_code[{{$serial->id}}]">
                                                            {{$serial->stock_move->stock_id_code }}
                                                        </td>
                                                        <td>
                                                            <input type="hidden" value="{{$serial->serial_no }}" name="serial_no[{{$serial->id}}]">
                                                            {{$serial->serial_no}}
                                                        </td>
                                                        
                                                        <td>
                                                            <input type="hidden" value="{{$serial->status }}" name="status[{{$serial->id}}]">
                                                            
                                                            {{ ucwords(str_replace('_',' ',$serial->status)) }}
                                                        </td>
                                                        
                                                        <td> {{$serial->odometer}} </td>


                                                        <td>
                                                            <input type="hidden" value="{{$serial->transtype }}" name="transtype[{{$serial->id}}]">

                                                            <input type="hidden" value="" name="vehicle_reg_no[{{$serial->id}}]">
                                                            <input type="checkbox" value="1" name="deallocate_serial[{{$serial->id}}]">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <th class="text-center" colspan="6">The Tyre does not have any retread item </th>
                                                </tr>
                                            @endif
                                        </tbody> 
                                    </table>
                                </div>
                                <div class="col-md-12 text-right">
                                    <input type="hidden" name="tyre_inventory_id" value="{{@request()->tyre_inventory_id}}">
                                    <input type="hidden" name="odometer" value="{{@request()->odometer}}">
                                    <input type="submit" value="Receive" class="btn btn-primary" name="submit">
                                </div>
                            </div>
                        </div>

                </form>        
        </section>
    @endif
    
    @endsection
    
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />

    <style>
        .select2.select2-container.select2-container--default
        {
            width: 100% !important;
        }
    </style>
@endsection


@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">


        var request_vehicle='{{request()->vehicle}}';

        if(request_vehicle){
            $('#odometer').attr('disabled',false);

        }
        
        var is_modal='{{ (request()->modal)?1:0 }}';
        if(is_modal==1){
            $('#AddModal').modal('show');
        }    

       

   

     
</script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}">

</script>

 <script type="text/javascript">
     $(function () {
        $(".mlselec6t").select2();
    });

        

        var tyre_inventory_id = "{{request()->tyre_inventory_id}}";
        
        $('.tyre_list').val(tyre_inventory_id); // Select the option with a value of '1'
        $('.tyre_list').trigger('change');  
</script>

   
@endsection

