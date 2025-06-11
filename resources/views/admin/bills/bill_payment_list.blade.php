
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
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                    <tr>
                                        
                                        <th width="10%"  >S.No</th>
                                        <th width="10%"  >Supplier Code</th>
                                        <th width="10%"  >Account</th>
                                        <th width="10%"  >Ref No</th>
                                        <th width="20%"  >Payment Date</th>
                                        <th width="10%"  >Paid Amount</th>
                                        <th width="10%"  >Transaction Date</th>
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
        "url": '{!! route('bills.bill_payment_list',$data->id) !!}',
                "dataType": "json",
                "type": "GET",
                "data":{ _token: "{{csrf_token()}}"}
        },
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_length').each(function () {
              $('.remove-btn').remove();
                $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-left ml-2 btn-sm" style="margin-right:51px" href="{{route('bills.list')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>');
            });
        },
        columns: [
            { mData: 'id', orderable: true,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'supplier_code', name: 'supplier_code', orderable:true },
        { data: 'account', name: 'account', orderable:true  },
        { data: 'ref_no', name: 'ref_no', orderable:true  },
        { data: 'payment_date', name: 'payment_date', orderable:true },
        { data: 'totalAmount', name: 'totalAmount', orderable:true },
        { data: 'created_at', name: 'created_at', orderable:true },
        // { data: 'opening_balance', name: 'opening_balance', orderable:false },
        // { data: 'links', name: 'links', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
            { className: 'text-center', targets: [1] },
        ]
        , language: {
            searchPlaceholder: "Search"
        },
    });
});
   

     
</script>
   
@endsection
