
@extends('layouts.admin.admin')
@section('content')

<style type="text/css">
    /* Style the tab */
.tab {
  overflow: hidden;
/*  border: 1px solid #ccc;
*/  background-color: #c1ccd1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 7px 16px;
  transition: 0.3s;
  font-size: 14px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: white;
  border: 1px solid gainsboro;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
.ctable {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

.ctable,td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}
</style>

<section class="content">
    <div class="box box-primary">
        <div class="box-header"></div>
        @include('message')
        <div class="data-description">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                            
                        @include('admin/vehicle/add_tabs')
                        
                        <div class="col-md-12">
                            <br>
                            <h4>Service History - </h4>  
                            <hr>      
                        </div>

                        <br>
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover" id="dataTable" style="text-align:left !important" >
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
                            </table>      
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

    $(function() {
        var table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[0, "desc" ]],
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            "ajax":{
            "url": '{!! route('vehicle.show.servicehistory',$row->id) !!}',
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

<style type="text/css">
.cont {
  text-align: justify;
  text-justify: inter-word;;
}
</style>

@endsection