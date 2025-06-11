
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
                            <th  width="20%" class="noneedtoshort" >Action</th>
                        </tr>
                    </thead>

                    

                </table>
            </div>
        </div>
    </div>

</section>

@endsection

@section('uniquepagescript')
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
  <div class="loader" id="loader-1"></div>
</div>
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
        "url": '{!! route('maintain-suppliers.datatable') !!}',
                "dataType": "json",
               
                "data":{ _token: "{{csrf_token()}}",'is_verified':'no'}
        },
        columns: [
        { data: 'supplier_code', name: 'supplier_code', orderable:true },
        { data: 'name', name: 'name', orderable:true  },
        { data: 'address', name: 'address', orderable:true },
        { data: 'actions', name: 'actions', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
            { className: 'text-center', targets: [1] }
        ]
        , language: {
            searchPlaceholder: "Search"
        },
    });
});
    
</script>
@endsection