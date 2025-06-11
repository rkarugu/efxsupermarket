
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
                                <form  method="post">
                                    {{csrf_field()}}
                                        
                                        <div class="row">
                                            
                                            <div class="col-md-3 form-group">
                                                <label for="">Date From</label>
                                                <input type="date" name="from" id="from" class="form-control">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label for="">Date Upto</label>
                                                <input type="date" name="to" id="to" class="form-control">
                                            </div>
                                            <div class="col-md-3 ">
                                                <br>
                                                {{-- <button type="submit" class="btn btn-secondary">Report</button> --}}
                                                <button type="button" id="filter" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                    <tr>
                                        
                                        <th width="10%"  >S.No.</th>
                                        <th width="10%"  >User</th>
                                        <th width="30%"  >Document No.</th>
                                        <th width="10%"  >Dated</th>
                                        <th width="20%"  >No. of Adjustments</th>
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
    <script type="text/javascript">
       
        
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('inventory-item-adjustment.index') !!}',
        "dataType": "json",
        "type": "GET",
        data:function(data){
            data.processed=$('#is_processed').val();
            var from = $('#from').val();
            var to = $('#to').val();

            data.from = from;
            data.to = to;
        }
        },
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();
              $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" href="{{route('inventory-item-adjustment.create')}}">Add Adjustment</a>');
            });
        },
        columns: [
            { mData: 'id', orderable: true,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'name', name: 'name', orderable:true  },
        { data: 'document_no', name: 'document_no', orderable:true  },
        { data: 'dated', name: 'dated', orderable:true },
        { data: 'no_of_adjustment', name: 'no_of_adjustment', orderable:false },
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
function printBill(slug)
    {
        jQuery.ajax({
            url: $(slug).attr('href'),
            type: 'GET',
            async:false,   //NOTE THIS
            headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
            success: function (response) {
            var divContents = response;
            var printWindow = window.open('', '', 'width=600');
            printWindow.document.write(divContents);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
            }
        });
              
    }

     
</script>
   
@endsection
