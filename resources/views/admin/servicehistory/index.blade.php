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
                                         

                                          <th width="20%">Vehicle</th>
                                          <th class="text-left" width="20%">Completed</th>
                                          <th width="20%">Vendor</th>
                                          <!-- <th width="20%">Issues</th> -->
                                          <!-- <th width="20%">Issues</th> -->
                                          <th width="20%">Meter</th>
                                          <th  width="20%" class="noneedtoshort" >Action</th>
                                    </tr>
                                    </thead> 
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                  <!--  <tfoot>
                                       <tr>
                                           <td colspan="4"><b>Total<b></td>
                                           <td id="amount"></td>
                                       </tr>
                                   </tfoot> -->
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
 @section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">
       
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('servicehistory.index') !!}',
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
        @if(isset($permission['servicehistory___create']) || $permission == 'superadmin')
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
         { data: 'vehicle_list_license_plate', name: 'vehicle_list_license_plate', orderable:true  },
        { data: 'completion_date', name: 'completion_date', orderable:false  },
        { data: 'wa_suppliers_name', name: 'wa_suppliers_name', orderable:false  },
        // { data: 'issues', name: 'issues', orderable:false  },
        // { data: 'issues_types_issues', name: 'issues_types_issues', orderable:false  },
        { data: 'odometer', name: 'odometer', orderable:false  },
        // { data: 'total', name: 'total', orderable:false  },

        // { data: 'total', name: 'total', orderable:false  },
        // { data: 'fuel_economy', name: 'fuel_economy', orderable:false  },
        // { data: 'cost_per_meter', name: 'cost_per_meter', orderable:false  },

        

        // { data: 'type', name: 'type', orderable:true  },
        // { data: 'model', name: 'model', orderable:true  },
        // { data: 'photos', name: 'photos', orderable:true  },
        // { data: 'status', name: 'status', orderable:true  },

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
   
@endsection

