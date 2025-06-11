
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
            @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
            <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a></div>
            @endif
            <br>
            @include('message')
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th width="20%"  >Supplier Code</th>
                            <th width="20%"  >Name</th>
                            <th width="20%"  >Address</th>
                            <th width="10%"  >Balance</th>
                            <th  width="20%" class="noneedtoshort" >Action</th>
                        </tr>
                    </thead>

                    <tfoot>
                        
                    <td></td>
                     <td></td>
                      <td style="font-weight: bold;">Total</td>
                       <td style="font-weight: bold;" id="total_balane">0.00</td>
                        <td></td>

                    </tfoot>

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
        "url": '{!! route('maintain-suppliers.datatable') !!}',
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
        },
        columns: [
        { data: 'supplier_code', name: 'supplier_code', orderable:true },
        { data: 'name', name: 'name', orderable:true  },
        { data: 'address', name: 'address', orderable:true },
        { data: 'total_amount_inc_vat', name: 'total_amount_inc_vat', orderable:false },
        { data: 'action', name: 'action', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
            { className: 'text-center', targets: [1] }
        ]
        , language: {
            searchPlaceholder: "Search"
        },



    "drawCallback": function (settings) { 
        // Here the response
        //var response = JSON.parse(settings);
       var total_balane = settings.json.totalBalance;

       $("#total_balane").html(total_balane);
    },



    });
});
    
</script>
@endsection