@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
           
        </div>
         @include('message')
         <div class="box-body" style="padding-bottom:15px">
            <form action="" method="get">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="">From</label>
                    <input type="date" name="start-date" id="start-date" class="form-control" value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="">To</label>
                    <input type="date" name="end-date" id="end-date" class="form-control"  value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                    <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                    </div>
                </div>
            </div>
            </form>
         <div class="table-responsive">
             <table class="table table-hover table-bordered table-invert" id="dataTable" style="width: 100%">
                 <thead>
                     <tr>
                         <th style="width:8%">Sr. No.</th>
                         <th style="width:10%">Date</th>
                         <th style="width:10%">Shift No</th>
                         <th style="width:10%">Document No</th>
                         <th style="width:11%">Store Location</th>
                         <th style="width:10%">No of line items</th>
                         <th style="width:10%">Un-Fullfilled</th>
                         <th style="width:10%">Store C Reuistion Generated</th>
                         <th  style="width:10%">Action</th>
                     </tr>
                 </thead>
                 <tbody>
                     
                 </tbody>
                 <!-- <tfoot>
                     <tr>
                         <th colspan="6">Total</th>
                         <th id="getTotal">
                         </th>
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
<script>
    function printBill(slug)
    {
        let isConfirm = confirm('Do you want to print this POS?');
        if (isConfirm) {
            print_this($(slug).attr('href'));
        }
    }
          
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route($model.'.index') !!}',
        "dataType": "json",
        "type": "GET",
        data:function(data){
            var from = $('#start-date').val();
            var to = $('#end-date').val();
            data.from = from;
            data.to = to;
        },
        "dataSrc": function (suc){
            if(suc.total){
                $('#getTotal').html(suc.total);
            }
            return suc.data;
        }
        },
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();

              {{--@if($permission == 'superadmin' || isset($permission['pos-cash-sales-r___add']))
                $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" href="{{route("pos-cash-sales-test.create")}}">Add {{$title}} R</a>');
              @endif
              
              @if($permission == 'superadmin' || isset($permission['pos-cash-sales___add']))
                    $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" href="{{route($model.'.create')}}">Add {{$title}}</a>');
              @endif--}}
            });
        },
        /*<th style="width:8%">Sr. No.</th>
                         <th style="width:10%">Date</th>
                         <th style="width:10%">Shift No</th>
                         <th style="width:11%">Store Location</th>
                         <th style="width:10%">No of line items</th>
                         <th style="width:10%">Un-Fullfilled</th>
                         <th  style="width:10%">Action</th>*/

        columns: [
                { mData: 'id', orderable: true,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }},
                { data: 'created_at', name: 'created_at', orderable:true  },
                { data: 'shift_no', name: 'shift_no', orderable:true  },
                { data: 'document_no', name: 'document_no', orderable:true  },
                { data: 'store_location_id', name: 'store_location_id', orderable:false },
                { data: 'no_of_line_items', name: 'no_of_line_items', orderable:true },
                { data: 'new_un_fullfilled', name: 'new_un_fullfilled', orderable:false },
                { data: 'is_requisition_done', name: 'is_requisition_done', orderable:false },
                { data: 'links', name: 'links', orderable:false},
            ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
        ],
        language: {
            searchPlaceholder: "Search"
        },
    });
    $('#filter').click(function(e){
        e.preventDefault();
        table.draw();
        $('#modelId').modal('hide');
    });

    $(document).on('click','.archive_btn', function(){
        if(confirm('Are you confirm for archive this item?')){
           return true; 
        }
        return false;    
    });
});
   
</script>
@endsection