
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
                            <h4>Issues - </h4>  
                            <hr>      
                        </div>

                        <br>
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover" id="dataTable" style="text-align:left !important" >
                                <thead>
                                    <tr>
                                         

                                          <th>Asset Name</th>
                                          <th>Asset Record Type</th>
                                          <th class="text-left">Issue</th>
                                          <th>Summary</th>
                                          <th class="noneedtoshort" >Issue Status</th>
                                          <th>Reported Date</th>
                                          <th>Assigned</th>
                                          <th>Labels</th>
                                      
                                      
                            
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
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
            "url": '{!! route('vehicle.show.issues',$row->id) !!}',
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
            @if(isset($permission['issues___create']) || $permission == 'superadmin')
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
            
            /*<th>Asset Name</th>
                                              <th>Asset Record Type</th>
                                              <th class="text-left">Issue</th>
                                              <th>Summary</th>
                                              <th class="noneedtoshort" >Issue Status</th>
                                              <th>Reported Date</th>
                                              <th>Assigned</th>
                                              <th>Labels</th>*/

            { data: 'vehicle_list_license_plate', name: 'vehicle_list_license_plate', orderable:true  },
            { data: 'asset_type', name: 'asset_type', orderable:true  },

            { data: 'id', name: 'id', orderable:false  },
            { data: 'summary', name: 'summary', orderable:false  },
            { data: 'links', name: 'links', orderable:false},
            { data: 'reported_date', name: 'reported_date', orderable:false  },
            { data: 'user_name', name: 'user_name', orderable:false  },
            { data: 'labels', name: 'labels', orderable:false  },
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