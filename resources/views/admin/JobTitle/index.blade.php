
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
                        <div class="box-header with-border no-padding-h-b">  <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a></div>
                            @include('message')<br>
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                    <tr>
                                    <th style="width: 5%;">S.no</th>
                                    <th>Job Title</th>
                                    <th>Min Salary</th>
                                    <th>Max Salary</th>
                                    <th>Description</th>
                                    <th class="noneedtoshort" style="width: 15%;" >Action</th>
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
        "url": '{!! route('jobTitles.Datatables') !!}',
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
        },
        columns: [
        { data: 'ID', name: 'ID', orderable:true },
        { data: 'job_title', name: 'job_title', orderable:true },
        { data: 'min_salary', name: 'min_salary', orderable:true },
        { data: 'max_salary', name: 'max_salary', orderable:true },
        { data: 'Description', name: 'Description', orderable:true },
        { data: 'action', name: 'action', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
        ]
        , language: {
        searchPlaceholder: "Search"
        },
    });
});
</script>
@endsection
