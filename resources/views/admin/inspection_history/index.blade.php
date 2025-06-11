@extends('layouts.admin.admin')

@section('content')

<style type="text/css">
    .rounded_box{
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 8px 8px 8px 17px;
    }
</style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            
                            <div class="col-sm-10">
                                <h3 class="box-title"> {!! $title !!} <a style="text-align: right;" href="{{ (url()->previous())?url()->previous():route('inspection_history.index') }}" class="btn btn-primary">Back</a> </h3>

                            </div>
                            <div class="col-sm-2 text-right">
                                @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                                    <div align = "right"> <a href="javascript:void()" data-toggle="modal" data-target=".inspection-form" class = "btn btn-success">Start Inspection</a>
                                    </div>
                                @endif
                            </div>

                            

                            
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
                            </style>
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>
                                        <th>Vehicle</th>
                                        <th>Group</th>
                                        <th>Submited</th>
                                        <th>Duration</th>
                                        <th>Inspection Form</th>
                                        <th>User</th>
                                        <th>Location Exception</th>
                                        <th>Failed Items</th>
                                        <th  class="noneedtoshort" >Action</th>
                                       
                                    </tr>
                                    </thead> 

                                    <tbody></tbody>
                                       
                                  
                                </table>
                            </div>
                        </div>
                    </div>


    </section>

<!-- Start Inspection Modal -->

<div class="modal fade inspection-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel">Select Inspection Form</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="container-fluid">  
                <p>ALL</p>
                <div class="col-sm-12">

                    @foreach($forms as $form)
                        <div class="row">
                            <a href="{{route('inspection_history.create',base64_encode($form->id))}}">
                                <div class="col-md-12 rounded_box">
                                    {{ $form->title }}<br>
                                    
                                    <!-- Driver Vehicle Inspection Report (Simple) -->
                                    
                                </div>
                            </a>
                        </div>
                    @endforeach
                    
                </div>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
<!-- End Inspection Modal -->
   
@endsection
 @section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">
       
$(function() {
    var is_modal='{{ (request()->modal)?1:0 }}';
    if(is_modal==1){
        $('.inspection-form').modal('show');
    }

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('inspection_history.index') !!}',
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
        @if(isset($permission['vehicle___create']) || $permission == 'superadmin')
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();
              // $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" data-toggle="modal" data-target="#AddModal" href="#">Add</a>');
            });
        },
        @endif

        /*<th width="20%"  >Name</th>
          <th width="20%"  >Year</th>
          <th width="10%"  >Make</th>
          <th width="10%"  >Model</th>
          <th width="10%"  >Status</th>
          <th width="20%"  >Type</th>
          <th width="10%"  >Current Meter</th>
          <th width="10%"  >License Platf</th>*/

        
          
        columns: [
            { mData: 'id', orderable: true,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'vehicle_id', name: 'vehicle_id', orderable:false  },    
        { data: 'management', name: 'management', orderable:false  },    
        { data: 'created_at', name: 'created_at', orderable:false  },
        { data: 'duration', name: 'duration', orderable:false  },
        { data: 'inspection_form_id', name: 'inspection_form_id', orderable:false  },
        { data: 'user_id', name: 'user_id', orderable:false  },
        { data: 'location', name: 'location', orderable:false  },
        { data: 'failed_item', name: 'failed_item', orderable:false  },
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
   
$(document).on('submit','.archiveMe',function(e){
    e.preventDefault();
    var $this = this;
    Swal.fire({
      title: 'Do you want to archive this?',
      showCancelButton: true,
      confirmButtonText: `Procced`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        return true;
      }
    })

});
     
</script>
   
@endsection

