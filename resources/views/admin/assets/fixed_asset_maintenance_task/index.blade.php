
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
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Description</th>
                                            <th>Asset</th>
                                            <th>Responsible Name</th>
                                            <th>Manager Name</th>
                                            <th class="noneedtoshort" >Action</th>
                                        </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
    @endsection
    @section('uniquepagestyle')


 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #note{
    height: 60px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection
    @section('uniquepagescript')
    <script src="{{asset('public/js/sweetalert.js')}}"></script>
    <script src="{{asset('public/js/form.js')}}"></script>
    <script type="text/javascript">
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('fixed_asset_maintenance_task_list') !!}',
                "dataType": "json",
                "type": "GET",
                "data":{ _token: "{{csrf_token()}}"}
        },'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();
                $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm"  style="margin-left:5px" href="{{route('fixed_asset_maintenance_task_add')}}">Add Asset</a>');
            });
        },
        columns: [
        { data: 'id', name: 'id', orderable:true, render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'task_description', name: 'task_description', orderable:true  },
        { data: 'asset_description_short', name: 'asset_description_short', orderable:true  },
        { data: 'responsible_name', name: 'responsible_name', orderable:true },
        { data: 'manager_name', name: 'manager_name', orderable:true },
        { data: 'links', name: 'action', orderable:false},
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
     
    $(function() {
    var form = new Form();
    $(document).on('submit','.addAssetParts',function (e) { 
        e.preventDefault();
       
        var data = $(this).serialize();
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            var $this = $(this);
            var form = new Form();

            $.ajax({
                url:url,
                method:method,
                data:data,
                success:function(out)
                {
                    $(".remove_error").remove();
                    if(out.result == 0) {
                        console.log(out.errors);
                        for(let i in out.errors) {                        
                            $this.find("[name='"+i+"']").
                            parent().
                            append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                        }
                    }
                    if(out.result === 1) {
                        form.successMessage(out.message);
                        $this.find('input:not(:hidden)').val('');
                        $this.find('textarea').val('');
                       
                        if(out.refresh)
                        {
                            setTimeout(() => {
                                location.href = '{{route('fixed_asset_maintenance_task_list')}}';
                            }, 1000);
                        }
					}
                    if(out.result === -1) {
						form.errorMessage(out.message);							
					}
                },
                error:function(err)
                {
                    $(".remove_error").remove();
                    $('.loder').css('display','none');
                    form.errorMessage('Something went wrong');							
                }
            });
    });
});
</script>
@endsection
