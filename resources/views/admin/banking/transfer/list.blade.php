
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
                            <div class="col-md-12 no-padding-h table-responsive">
                                <form action="{{route('banking.transfer.pdf')}}" method="post">
                                    {{csrf_field()}}
                                        
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="">Date From</label>
                                                <input type="date" name="from" id="from" class="form-control">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="">Date Upto</label>
                                                <input type="date" name="to" id="to" class="form-control">
                                            </div>
                                            <div class="col-md-4 ">
                                                <br>
                                                <button type="submit" class="btn btn-secondary">Report</button>
                                                <button type="button" id="filter" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                            <div class="col-md-12 no-padding-h  table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                    <tr>                                        
                                        <th width="10%"  >S.No</th>
                                        <th width="10%"  >Transfer From</th>
                                        <th width="20%"  >Transfer to</th>
                                        <th width="10%"  >Date</th>
                                        <th width="10%"  >Amount</th>
                                        <th width="10%"  >Memo</th>
                                    </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
    @endsection
    @section('uniquepagescript')
    <script type="text/javascript">
       
        
$(function() {
    var table = $('#dataTable').DataTable({
        
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('banking.transfer.list') !!}',
                "dataType": "json",
                "type": "GET",
                data:function(data){
                    var from = $('#from').val();
                    var to = $('#to').val();

                    data.from = from;
                    data.to = to;
                }
        },
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();
                $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" href="{{route('banking.transfer.new')}}">Add</a>');
            });
        },
        columns: [
            { mData: 'id', orderable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'from_code', name: 'from_code', orderable:true },
        { data: 'to_code', name: 'to_code', orderable:true  },
        { data: 'date', name: 'date', orderable:true  },
        { data: 'amount', name: 'amount', orderable:true },
        { data: 'memo', name: 'memo', orderable:true },
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
