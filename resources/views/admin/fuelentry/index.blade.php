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

                                <table class="table table-bordered table-hover" id="dataTable" style="text-align:left !important" >

                                <!-- <table class="table table-bordered table-hover" id="dataTable"> -->
                                     <style>
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
                            </style>
                        <div class="ml-2">
                            
                        <div class="ml-2 flex items-center first:ml-0">
                            
                                   <span class="font-sans m-0 text-sm text-gray-900 text-left font-normal normal-case break-words max-w-full">Total Fuel Cost</span>
                        <div class="w-full first:mt-0 mt-0">
                            
                                   <span class="font-sans m-0 text-xl text-gray-900 text-left font-semibold normal-case break-words max-w-full"><b>KES&nbsp;0.00</b></span>
                        </div>
                        </div>
                        <div class="ml-2 flex items-center first:ml-0">
                            
                                   <span class="font-sans m-0 text-sm text-gray-900 text-left font-normal normal-case break-words max-w-full">Total Volume</span>
                        <div class="w-full first:mt-0 mt-0">
                                   <span class="font-sans m-0 text-xl text-gray-900 text-left font-semibold normal-case break-words max-w-full"><b>0.00</b><small> gallons</small></span>
                        </div>
                        </div>
                         <div class="ml-2 flex items-center first:ml-0">
                            
                                   <span class="font-sans m-0 text-sm text-gray-900 text-left font-normal normal-case break-words max-w-full">Avg. Fuel Economy (Distance)</span>
                        <div class="w-full first:mt-0 mt-0">
                                   <span class="font-sans m-0 text-xl text-gray-900 text-left font-semibold normal-case break-words max-w-full"><b>0.00</b><small> MPG (US)</small></span>
                        </div>
                        </div>

                         <div class="ml-2 flex items-center first:ml-0">
                            
                                   <span class="font-sans m-0 text-sm text-gray-900 text-left font-normal normal-case break-words max-w-full">Avg. Fuel Economy (Hours)</span>
                        <div class="w-full first:mt-0 mt-0">
                                   <span class="font-sans m-0 text-xl text-gray-900 text-left font-semibold normal-case break-words max-w-full">--</span>
                        </div>
                        </div>

                         <div class="ml-2 flex items-center first:ml-0">
                            
                                   <span class="font-sans m-0 text-sm text-gray-900 text-left font-normal normal-case break-words max-w-full">Avg. Cost</span>
                        <div class="w-full first:mt-0 mt-0">
                                   <span class="font-sans m-0 text-xl text-gray-900 text-left font-semibold normal-case break-words max-w-full"><b>KES 0.00</b><small>/ gallon</small></span>
                        </div>
                        </div>
                        </div>
                        <br>


                                    <thead>
                                    <tr>
                                        <!-- <th width="10%">S.No.</th> -->
                                          <th width="20%"  >Vehicle</th>
                                          <th width="20%"  >Date</th>
                                          <th width="20%"  >Vendor</th>
                                          <th >Previous Odometer Reading</th>
                                          <th>Current Entry</th>
                                          <th>Distance</th>
                                          <th>Fuel Used</th>
                                          <th>Rate</th>
                                          <th width="20%"  >Usage</th>
                                          <th width="20%"  >Volume</th>
                                          <th width="10%"  >Total Amount</th>
                                          <th width="10%"  >Fuel Economy</th>
                                          <th width="10%"  >Cost per Meter</th>
                                          <th  width="20%" class="noneedtoshort" >Action</th>
                                      
                                      
                            
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                    </tr>
                                    </thead> 
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                      <tfoot>
                                       <tr>
                                           <td colspan="6"><b>Total<b></td>
                                           <td id="amount"></td>
                                           <td></td>
                                       </tr>
                                   </tfoot>
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
        "url": '{!! route('fuelentry.index') !!}',
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
        @if(isset($permission['fuelentry___create']) || $permission == 'superadmin')
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
        { data: 'fuel_entry_date', name: 'fuel_entry_date', orderable:false  },
        // { data: 'odometer', name: 'odometer', orderable:false  },
        { data: 'wa_suppliers_name', name: 'wa_suppliers_name', orderable:false  },

        { data: 'previous_odometer_reading', name: 'previous_odometer_reading', orderable:false  },
        { data: 'meter', name: 'meter', orderable:false  },
        { data: 'distance', name: 'distance', orderable:false  },
        { data: 'fuel_used', name: 'fuel_used', orderable:false  },
        { data: 'rate', name: 'rate', orderable:false  },
        { data: 'gallons', name: 'gallons', orderable:false  },
        { data: 'price', name: 'price', orderable:false  },

        { data: 'total', name: 'total', orderable:false  },
        { data: 'fuel_economy', name: 'fuel_economy', orderable:false  },
        { data: 'cost_per_meter', name: 'cost_per_meter', orderable:false  },

        

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

