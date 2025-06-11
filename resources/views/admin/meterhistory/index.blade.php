
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
                            <div class="col-sm-10">
                                <h3 class="box-title"> {!! $title !!} </h3>
                            </div>
                            <div class="col-sm-2 text-right">
                                <a style="text-align: right;" href="{{ (url()->previous())?url()->previous():route('meterhistory.index') }}" class="btn btn-primary">Back</a> 
                            </div>

                            @include('message')
                          
                            <style>
                                .table tr td{
                                    text-align:left !important
                                }
                            </style>
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" style="text-align:left !important" >
                                    <thead>
                                    <tr>
                                        
                                        <th width="10%"  >S.No.</th>
                                        <th width="30%" style="text-align:left" >Vehicle</th>
                                        <th width="30%" style="text-align:left" >Last Odometer Reading Date</th>
                                        <th width="30%" style="text-align:left" >Last Odometer Reading</th>
                                        <!-- <th width="70%" style="text-align:left" >Void</th> -->
                                        <th  width="20%" class="noneedtoshort" >Action</th>
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                    </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
 
    <!-- Modal -->
    <div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="{{route('meterhistory.store')}}" method="POST" class="submitMe">
            {{csrf_field()}}
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Meter</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                           <label for="">Vehicle</label>
                           <select class="form-control category_list m-bot15" name="vehicle" required="true"> 
                                @if(request()->vehicle_id && request()->license_plate)
                                <option value="{{request()->vehicle_id}}">{{request()->license_plate}}</option>
                               @endif
                           </select>
                        </div>

                        <div class="form-group">
                            <label for="date">Entry Type</label><br>
                            <select class="form-control">
                                <option value="">Selecy Type</option>
                                <option value="fuel_history">Fuel History</option>
                                <option value="service_history">Service History</option>
                                <option value="inspection_history">Inspection History</option>
                                <option value="tyre_fitting">Tyre Fitting</option>
                                <option value="tyre_removal">Tyre Removal</option>
                                <option value="tyre_transfer">Tyre Transfer</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="">Primary Meter</label>
                           
                            <input type="text" name="primary_meter" id="primary_meter" class="form-control" placeholder="Primary Meter" aria-describedby="helpId">
                            <small>Last updated: 20,811 mi (2 days ago)</small>
                        </div>

                        <div class="form-group">
                            <label for="date">Date</label><br>
                            <input class="datebox date-input form-control" type="date" value="date"  id="date" name="date">
                        </div>



         
                      
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form  method="POST" class="submitMe">
            {{csrf_field()}}
            <input type="hidden" id="hiddenid" value="" name="id">
            <input type="hidden"  value="PATCH" name="_method">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Meter</h5>
                    </div>
                    <div class="modal-body">
                       <!--  <div class="form-group">
                            <label for="">Vehicle</label>
                            <input type="text" name="vehicle" id="editvehicle" class="form-control" placeholder="Vehicle" aria-describedby="helpId">
                        </div> -->
                        
                        <div class="form-group">
                           <label for="">Vehicle</label>
                           <select class="form-control category_list m-bot15" name="vehicle" required="true" id="editvehicle"> 
                                
                           </select>
                        </div>

                        <div class="form-group">
                            <label for="">Meter Value</label>
                            <input type="text" name="primary_meter" id="editprimary_meter" class="form-control" placeholder="Meter Value" aria-describedby="helpId">
                        </div>

                        <div class="form-group">
                            <label for="">Meter Date</label>
                            <input type="text" name="date" id="editdate" class="form-control" placeholder="Meter Date" aria-describedby="helpId">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
              //$(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" data-toggle="modal" data-target="#AddModal" href="#">Add</a>');
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
 var category_list = function(){
            $(".category_list").select2(
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
        category_list();

        </script>

   
@endsection

