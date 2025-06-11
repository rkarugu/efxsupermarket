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
                            @include('message')<br>
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                    <tr>
                                    <th style="width: 5%;">S.no</th>
                                    <th>Staff No.</th>
                                    <th>Employee Name</th>
                                    <th>ID Number</th>
                                    <th>NHIF Number</th>
                                    <th>NSSF Number</th>
                                   <!--  <th>Branch</th>
                                    <th>D.O.B</th>
                                    <th>Date Employed</th> -->
                                    <th class="noneedtoshort" style="width: 5%;" >Action</th>
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
        "url": '{!! route('OvertimeHours.Datatables') !!}',
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
        },
        columns: [
        { data: 'ID', name: 'ID', orderable:true },
        { data: 'staff_number', name: 'staff_number', orderable:true },
        { data: 'first_name', name: 'first_name', orderable:true },
        { data: 'Id_number', name: 'Id_number', orderable:true },
        { data: 'nhif_no', name: 'nhif_no', orderable:true },
        { data: 'nssf_no', name: 'nssf_no', orderable:true },
        { data: 'action', name: 'action', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
            { targets: [1] },
        ]
        , language: {
        searchPlaceholder: "Search"
        },
    });
});
</script>
<script type="text/javascript">
  
    $(".deletebtn").click(function(){
  alert("The paragraph was clicked.");
});

</script>
@endsection
