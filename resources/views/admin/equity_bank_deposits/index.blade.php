@extends('layouts.admin.admin')
@section('content')

<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> {!! $title !!} </h3>
        </div>       
            <div class="box-body">
                <form action="" method="get" >
                    <div class = "row">
                        <div class="col-sm-3">
                            <div class="form-group">
                            <label for="">From date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" placeholder="Select From Date" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                            <label for="">To date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" placeholder="Select To Date"  value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Salesman</label>
                                <select name="salesman" id="salesman" class="form-control select_select2">
                                    <option value="" selected disabled>Select Salesman</option>
                                    @foreach ($locations as $item)
                                        <option value="{{@$item->account_no}}">{{$item->location_name}} ({{$item->location_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-danger btn-sm" id="filter">Filter</button>
                        </div>
                    </div>  
                </form>
                 <table class="table table-hover table-bordered table-invert" id="dataTable" style="width: 100%">
                 <thead>
                     <tr>
                         <th >Sr. No.</th>
                         <th >Date</th>
                         <th >Customer Ref No.</th>
                         <th >Bank Ref</th>
                         <th >Bill Amount</th>
                         <th >Created At</th>
                         <th  >Action</th>
                     </tr>
                 </thead>
                 <tbody>
                     
                 </tbody>
                <tfoot>
                     <tr>
                         <th colspan="4">
                             Total
                         </th>
                         <th id="getTotal" colspan="3"> 
                         </th>
                     </tr>
                 </tfoot>
             </table>
            
            </div>
    </div>
</section>


@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />

@endsection

@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
   
$(function() {
    $('.select_select2').select2();
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
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                data.from_date = from_date;
                data.to_date = to_date;
                var salesman = $('#salesman').val();
                data.salesman = salesman;
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
              $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" href="{{route($model.'.create')}}">Add Pending Transaction</a>');
            });
        },
        columns: [
                { mData: 'id', orderable: true,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }},
                { data: 'transactionDate', name: 'transactionDate', orderable:true  },
                { data: 'CustomerRefNumber', name: 'CustomerRefNumber', orderable:true  },
                { data: 'bankreference', name: 'bankreference', orderable:true },
                { data: 'billAmount', name: 'billAmount', orderable:true },
                { data: 'created_at', name: 'created_at', orderable:true },
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
    });
});
   
</script>
@endsection


