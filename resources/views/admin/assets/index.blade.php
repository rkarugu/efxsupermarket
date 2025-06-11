
@extends('layouts.admin.admin')

@section('content')
<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Description Short</th>
                                            <th>Description Long</th>
                                            <th>Bar Code</th>
                                            <th>Serial No.</th>
                                            <th>Amount</th>
                                            <th class="noneedtoshort" >Action</th>
                                        </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
    @endsection
    @section('uniquepagestyle')


 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #note{
    height: 60px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection
    @section('uniquepagescript')
   
    <script type="text/javascript">
    $(document).ready(function() {
        $("#dataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('assets.index') !!}',
                    data: function(data) {
                        data.status = $("#status").val();
                        data.channel = $("#channel").val();
                        data.branch = $("#branch").val();
                        data.route = $("#route").val();
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
                    }
                },
                columns: [
                    { 
                        data: 'id', 
                        name: 'id', 
                        orderable:true, 
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }},
                    { 
                        data: 'asset_description_short', 
                        name: 'asset_description_short', 
                        orderable:true  
                    },
                    { 
                        data: 'asset_description_long', 
                        name: 'asset_description_long', 
                        orderable:true  
                    },
                    { 
                        data: 'bar_code', 
                        name: 'bar_code', 
                        orderable:false 
                    },
                    { 
                        data: 'serial_number', 
                        name: 'serial_number', 
                        orderable:true 
                    },
                    { 
                        data: 'amount', 
                        name: 'amount', 
                        orderable:true 
                    },
                    { 
                        data: 'links', 
                        name: 'action', 
                        orderable:false
                    },
                ],
                
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#debtorsTotal").text(json.total_amount);
                },
                fnDrawCallback: function (oSettings) {
                    $('.dataTables_filter').each(function () {
                    $('.remove-btn').remove();
                        $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm"  style="margin-left:5px" href="{{route('assets.add')}}">Add Asset</a>');
                    });
                },
                language: {
                    searchPlaceholder: "Search"
                }
            });

    });
// $(function() {
//     var table = $('#dataTable').DataTable({
//         processing: true,
//         serverSide: true,
//         order: [[0, "desc" ]],
//         pageLength: '<?= Config::get('params.list_limit_admin') ?>',
//         "ajax":{
//         "url": '{!! route('assets.index') !!}',
//                 "dataType": "json",
//                 "type": "GET",
//                 "data":{ _token: "{{csrf_token()}}"}
//         },
//         columns: [
//         { data: 'id', name: 'id', orderable:true, render: function (data, type, row, meta) {
//                 return meta.row + meta.settings._iDisplayStart + 1;
//             }},
//         { data: 'asset_description_short', name: 'asset_description_short', orderable:true  },
//         { data: 'asset_description_long', name: 'asset_description_long', orderable:true  },
//         { data: 'bar_code', name: 'bar_code', orderable:false },
//         { data: 'serial_number', name: 'serial_number', orderable:true },
//         { data: 'amount', name: 'amount', orderable:true },
//         { data: 'links', name: 'action', orderable:false},
//         ],
//         "columnDefs": [
//             { "searchable": false, "targets": 0 },
//             { className: 'text-center', targets: [1] },
//         ]
//         , language: {
//         searchPlaceholder: "Search"
//         },
//     });
//     });
     

</script>
@endsection
