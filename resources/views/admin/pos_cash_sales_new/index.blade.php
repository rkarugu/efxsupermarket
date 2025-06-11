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
                         <th style="width:10%">User</th>
                         <th style="width:10%">Cash Sales</th>
                         <th style="width:11%">Date/Time</th>
                         <th style="width:10%">Customer</th>
                         <th style="width:10%">Payment</th>
                         <th style="width:10%">Cash</th>
                         <th style="width:10%">Change</th>
                         <th style="width:10%">Total</th>
                         <th  style="width:10%">Action</th>
                     </tr>
                 </thead>
                 <tbody>
                     
                 </tbody>
                 <tfoot>
                     <tr>
                         <th colspan="8">
                             Total
                         </th>
                         <th id="getTotal">
                         </th>
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
              $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" href="{{route($model.'.create')}}">Add {{$title}}</a>');
            });
        },
        columns: [
                { mData: 'id', orderable: true,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }},
                { data: 'user_name', name: 'user_name', orderable:true  },
                { data: 'sales_no', name: 'sales_no', orderable:true  },
                { data: 'date_time', name: 'date_time', orderable:false },
                { data: 'customer', name: 'customer', orderable:true },
                { data: 'payment_title', name: 'payment_title', orderable:false },
                { data: 'cash', name: 'cash', orderable:true },
                { data: 'change', name: 'change', orderable:true },
                { data: 'total', name: 'total', orderable:false },
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
});
   
</script>
@endsection