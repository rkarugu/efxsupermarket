
@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                           
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                            </div>
                            <style>
                                .table tr td{
                                    text-align:left !important
                                }
                            </style>
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" style="text-align:left !important" >
                                    <thead>
                                    <tr>
                                        
                                        <th width="10%"  >S.No.</th>
                                        <th width="70%" style="text-align:left" >Title</th>
                                        <th  width="20%" class="noneedtoshort" >Action</th>
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                    </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
 
    <!-- Modal -->
    <div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="{{route('bodytype.store')}}" method="POST" class="submitMe">
            {{csrf_field()}}
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Body Type</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Title</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Title" aria-describedby="helpId">
                        </div>
                      
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form  method="POST" class="submitMe">
            {{csrf_field()}}
            <input type="hidden" id="hiddenid" value="" name="id">
            <input type="hidden"  value="PATCH" name="_method">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Body Type</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Title</label>
                            <input type="text" name="title" id="edittitle" class="form-control" placeholder="Title" aria-describedby="helpId">
                        </div>
                      
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endsection
    @section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">
       
        function openEditForm(id){
            $('#hiddenid').val('');
            $('#edittitle').val('');
            $('#editdescription').val('');
            $.ajax({
                type: 'GET',
                url: $(id).attr('href'),
                success: function (response) {
                    $('#hiddenid').val(response.data.id);
                    $('#edittitle').val(response.data.title);
                    $('#editModal form').attr('action',response.url);
                    $('#editModal').modal('show');
                }
            });
        }
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('bodytype.index') !!}',
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
        @if(isset($permission['bodytype___create']) || $permission == 'superadmin')
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();
              $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" data-toggle="modal" data-target="#AddModal" href="#">Add</a>');
            });
        },
        @endif
        columns: [
            { mData: 'id', orderable: true,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
        { data: 'title', name: 'title', orderable:true  },
        // { data: 'dated', name: 'dated', orderable:true },
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
   

     
</script>
   
@endsection

