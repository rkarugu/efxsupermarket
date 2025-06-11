@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a></div>
                             @endif
                            <br>
                            @include('message')
                        
                            <!-- <div class="col-md-12 no-padding-h"> -->
                            <div class="col-md-12 no-padding-h table-responsive">

                                <table class="table table-bordered table-hover custom-table" id="dataTable" style="text-align:left !important" >

                                <!-- <table class="table table-bordered table-hover" id="dataTable"> -->
                                     <style>
                                        .custom-table thead th{
                                            
                                            text-align: left!important;
                                          }

                                .table tr td{
                                    text-align:left !important

                                }
                                .ml-2 {
                                display: inline-block;
                                margin-bottom: 6px;
                                   }

                                            .ml-2.flex.items-center.first\:ml-0 {
                                                margin-right: 87px;
                                          }
                                          .top-box{
                                            display: flex !important;
                                            align-items: center !important;
                                          }
                                          .btn-div{
                                            padding-top: 10px !important;
                                            display: flex;
                                          }
                                           .btn-div button{
                                            margin-right: 10px;
                                           }


                            </style>
                       


                                    <thead>
                                    <tr>
                                        <!-- <th width="10%">S.No.</th> -->
                                         

                                          <th>Vehicle</th>
                                          <th>Service Task</th>
                                          <th>Status</th>
                                          <th>Next Due</th>
                                          <th>Last Completed</th>
                                          <th>Action</th>
                                    </tr>
                                    </thead> 
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
 @section('uniquepagescript')
     <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    
    
    <script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">
       
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('service_remainder.index') !!}',
        "dataType": "json",
        "type": "GET",
        data:function(data){
            data.processed=$('#is_processed').val();
            var from = $('#from').val();
            var to = $('#to').val();

            data.from = from;
            data.to = to;
        },"dataSrc": function (suc){
                if(suc.total){
                    $('#amount').html(suc.total).css("font-weight","Bold");
                }
                return suc.data;
            }
        },
        @if(isset($permission['service_remainder___create']) || $permission == 'superadmin')
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();
              // $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" data-toggle="modal" data-target="#AddModal" href="#">Add</a>');
            });
        },
        @endif
        columns: [
            // { mData: 'id', orderable: true,
            // render: function (data, type, row, meta) {
            //     return meta.row + meta.settings._iDisplayStart + 1;
            // }},
        { data: 'vehicle_license_plate', name: 'vehicle_license_plate', orderable:true  },
        { data: 'service_task_name', name: 'service_task_name', orderable:false  },
        { data: 'status', name: 'status', orderable:false  },
        { data: 'main_next_due_date', name: 'main_next_due_date', orderable:false  },
        { data: 'last_completed', name: 'last_completed', orderable:false  },
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

   $('table').on('click','.delete-confirm', function (event) {

      event.preventDefault();
      const url = $(this).attr('href');
      swal({
          title: 'Are you sure? you want to archive this item',
          text: '',
          icon: 'warning',
          buttons: ["No,Cancel It", "Yes,I Confirm"],
          }).then(function(value) {
          if (value) {
          window.location.href = url;
        }
      });
     });
   

     
</script>

   

   
@endsection

