
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
                                <a style="text-align: right;" href="{{route('meterhistory.index') }}" class="btn btn-primary">Back</a> 
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
                                        <th style="text-align:left" >Vehicle</th>
                                        <th style="text-align:left" >Date</th>
                                        <th style="text-align:left" >Odometer Reading</th>
                                        <th style="text-align:left" >Entry Type</th>
                                        <th style="text-align:left" >User</th>
                                    </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
 
    
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
        "url": '{!! route('meterhistory.odometer_reading_history',$history_id) !!}',
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
        columns: [
            { mData: 'id', orderable: true,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'vehicle_list_license_plate', name: 'vehicle_list_license_plate', orderable:false  },
        { data: 'date', name: 'date', orderable:false  },
        { data: 'odometer_reading', name: 'odometer_reading', orderable:false  },
        { data: 'entry_type', name: 'entry_type', orderable:false  },
        { data: 'user_id', name: 'user_id', orderable:false  },
        //{ data: 'links', name: 'links', orderable:false},
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

