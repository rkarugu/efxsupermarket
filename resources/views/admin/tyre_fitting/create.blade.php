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
                            <div class="col-md-2">
                                <div class="form-group">
                                <label for="">Date</label>
                                <input type="date" value="{{date('Y-m-d')}}" placeholder="Select Date" class="form-control" name="vehicle_date" id="vehicle_date">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                   <label for="">Vehicle</label>
                                   
                                   {!!Form::select('vehicle', getVehicleList(),request()->vehicle, ['class' => 'form-control mlselec6t vehicle_list','required'=>true,'placeholder' => 'Please select','id'=>'wa_customer_id' ])!!} 
                                </div>
                            </div>
                           
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                <label for="">Odometer</label>
                                <input type="number" required value="{{@request()->odometer}}" disabled class="form-control" name="odometer" id="odometer">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                <label for="">No Of Wheels</label>
                                <input type="number" value="{{@request()->number_of_wheels}}" disabled   class="form-control" name="number_of_wheels" id="number_of_wheels">
                                </div>
                            </div>
                            <!-- <div class="col-md-1 text-center">
                                <label for="">&nbsp;</label>
                                <button type="submit" class="btn btn-primary subme" value="filter" name="manage">Get Data</button>
                            </div> -->
                        </div>


                        <div class="row" id="selectTyreDiv" style="display:none;">
                            <div class="col-md-3">
                                <div class="form-group">
                                <label for="">Select Tyre</label>
                                    
                                    {!!Form::select('tyre_inventory_id', getTyreList(),request()->tyre_inventory_id, ['class' => 'form-control mlselec6t tyre_inventory_id','required'=>true,'placeholder' => 'Please select','id'=>'wa_customer_id' ])!!} 
                                </div>
                            </div>

                            

                            {{--<div class="col-md-2">
                                <div class="form-group">
                                   <label for="">Type</label>
                                   <select class="form-control m-bot15" name="type" id="type"> 
                                        <option value="">Select Types</option>
                                        <option {{(@request()->type && @request()->type=="new"?'selected':'')}} value="new">New</option>
                                        <option {{(@request()->type && @request()->type=="retread"?'selected':'')}} value="retread">Retread</option>
                                   </select>
                                </div>
                            </div> --}}
                           
                            
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

    @if(request()->vehicle)

        <section class="content">
            <!-- Small boxes (Stat box) -->
                <form method="post" action="{{route($model.'.store')}}" class="submitMe">
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
                                                <th>Tyre Position</th>
                                                <th>Allocate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($tyre_serials))
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

                                                        <td>
                                                            {{ Form::select('tyre_position_id['.$serial->id.']', getTyrePositionList(),null, ['class' => 'form-control mlselec6t tyre_position_list','placeholder' => 'Please select','id'=>'tyre_position_id' ]) }} 
                                                        </td>
                                                        
                                                        <td>
                                                            <input type="hidden" value="{{$serial->transtype }}" name="transtype[{{$serial->id}}]">
                                                            <input type="hidden" value="{{$vehicle_reg_no }}" name="vehicle_reg_no[{{$serial->id}}]">
                                                            <input type="checkbox" value="1" name="allocate_serial[{{$serial->id}}]">
                                                        </td>
                                                        
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody> 
                                    </table>
                                </div>
                                <div class="col-md-12 text-right">
                                    <input type="hidden" name="vehicle_date" value="{{request()->vehicle_date}}">
                                    <input type="hidden" name="vehicle" value="{{@request()->vehicle}}">
                                    <input type="hidden" name="odometer" value="{{@request()->odometer}}">
                                    <input type="hidden" name="number_of_wheels" value="{{request()->number_of_wheels}}">
                                    <input type="hidden" name="tyre_inventory_id" value="{{request()->tyre_inventory_id}}">
                                    <input type="hidden" name="type" value="{{@request()->type}}">
                                    <input type="submit" value="Submit" class="btn btn-primary" name="submit">
                                </div>
                            </div>
                        </div>

                </form>        
        </section>


        @if($allocated_data->count()>0)

            <section class="content">
                <h3>Tyres Already Allocated</h3>
            <!-- Small boxes (Stat box) -->
                <form method="post" action="{{route($model.'.store')}}" class="submitMe">
                        {{csrf_field()}}
                        {{method_field('POST')}}

                        <div class="box box-primary">
                            <div class="box-header with-border no-padding-h-b">
                               
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
                                                <th>Trans Type</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($allocated_data))
                                                @foreach($allocated_data as $allo_key => $allocated)
                                                    <tr>
                                                        <td>{{++$allo_key}}</td>
                                                        <td>
                                                            {{$allocated->stock_move->stock_id_code }}
                                                        </td>
                                                        <td>
                                                            {{$allocated->serial_no}}
                                                        </td>
                                                        <td>
                                                            {{$allocated->transtype}}
                                                        </td>
                                                        <td>
                                                            {{ ucwords(str_replace('_',' ',$allocated->status)) }}
                                                        </td>
                                                        
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                        </div>

                </form>        
            </section>
        @endif
        
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
            $('#selectTyreDiv').show();

        }
        
        var is_modal='{{ (request()->modal)?1:0 }}';
        if(is_modal==1){
            $('#AddModal').modal('show');
        }    

        function openEditForm(id){
            $('#hiddenid').val('');
            $('#editvehicle').val('');
            $('#editprimary_meter').val('');
            $('#editdate').val('');
            // $('#editdescription').val('');
            $.ajax({
                type: 'GET',
                url: $(id).attr('href'),
                success: function (response) {
                    $('#hiddenid').val(response.data.id);
                    $('#editvehicle').val(response.data.vehicle);
                    $('#editprimary_meter').val(response.data.primary_meter);
                    $('#editdate').val(response.data.date);
                    $('#editModal form').attr('action',response.url);
                    $('#editModal').modal('show');
                }
            });
        }
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('meterhistory.index') !!}',
        "dataType": "json",
        "type": "GET",
        data:function(data){
            data.processed=$('#is_processed').val();
            var from = $('#from').val();
            var to = $('#to').val();

            data.from = from;
            data.to = to;
        }
        },
        @if(isset($permission['meterhistory___create']) || $permission == 'superadmin')
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();
              $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" data-toggle="modal" data-target="#AddModal" href="#">Add</a>');
            });
        },
        @endif
        columns: [
            { mData: 'id', orderable: true,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'vehicle_list_license_plate', name: 'vehicle_list_license_plate', orderable:false  },
        { data: 'date', name: 'date', orderable:false  },
        { data: 'primary_meter', name: 'primary_meter', orderable:false  },


        // { data: 'dated', name: 'dated', orderable:true },
        { data: 'links', name: 'links', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
            { className: 'text-center', targets: [1] },
        ]
        , language: {
        searchPlaceholder: "Search"
        },
    });
    $('#filter').click(function(){
        table.draw();
        $('#modelId').modal('hide');
    });
});
   

     
</script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}">

</script>

 <script type="text/javascript">
     $(function () {
        $(".mlselec6t").select2();
    });

        var vehicle_list = function(){
            $(".vehicle_list").select2(
            {
                placeholder:'Select vehicle',
                ajax: {
                    url: '{{route('vehicle.types')}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        vehicle_list();

        var vehicle = "{{request()->vehicle}}";
        
        $('.vehicle_list').val(vehicle); // Select the option with a value of '1'
        $('.vehicle_list').trigger('change');


        var tyre_inventory_id = "{{request()->tyre_inventory_id}}";
        
        $('.tyre_inventory_id').val(tyre_inventory_id); // Select the option with a value of '1'
        $('.tyre_inventory_id').trigger('change');
        
        var tyre_list = function(vehicle_id){
            $(".tyre_list").select2(
            {
                placeholder:'Select Tyre',
                ajax: {
                    url: '{{route('tyres.list')}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        console.log(data);
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };

        tyre_list();




    
    $('.vehicle_list').on('change',function(){
        if($(this).val()!=""){

            $('#odometer').attr('disabled',false);
            $('#number_of_wheels').attr('disabled',false);
            $('#selectTyreDiv').show();
        }
    });    
</script>

   
@endsection

