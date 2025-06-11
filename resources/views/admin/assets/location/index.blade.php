
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
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Assets Location </h3>
                    <div class="d-flex">
                        <a class="btn btn-primary mr-xs pull-right ml-2 btn-sm" href="{{route('assets.location.add')}}">Add Location</a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>location_ID</th>
                            <th>Location Description</th>
                            <th>Branch</th>
                            <th class="noneedtoshort" >Action</th>
                        </tr>
                    </thead>
                    
                </table>
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
        "url": '{{route('assets.location.index')}}',
                "dataType": "json",
                "type": "GET",
                "data":{ _token: "{{csrf_token()}}"}
        },
        // 'fnDrawCallback': function (oSettings) {
        //     $('.dataTables_filter').each(function () {
        //       $('.remove-btn').remove();
        //         $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm"  style="margin-left:5px" href="{{route('assets.location.add')}}">Add Location</a>');
        //     });
        // },
        columns: [
        { data: 'id', name: 'id', orderable:true, render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'location_ID', name: 'location_ID', orderable:true  },
        { data: 'location_description', name: 'location_description', orderable:true  },
        { data: 'branch.name', name: 'branch', orderable:true  },
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
                                location.href = '{{route('assets.location.index')}}';
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
