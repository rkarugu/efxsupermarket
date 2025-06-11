@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight: 500 !important;"> {{$title}} </h3>
                    <div class="d-flex">
                        <a href="{{ route('support-team.create') }}" class="btn btn-success"> <i class="fas fa-plus"></i> {{$title}}</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="categoryDataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Get Notified</th>
                                <th>Action</th>
                            </tr>
                        </thead>                        
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Delete Support</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                    <div class="box-body" id="deleteBody">
                        Are you sure you want to Delete?
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="deleteId" id="deleteId">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="confirmDeleteBtn" class="btn btn-primary" data-id="0" data-dismiss="modal">Delete</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>

@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }

</style>    
@endsection
@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>

<script>
    var form = new Form();
    $(document).ready(function() {
        
        $("#categoryDataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 100,
            ajax: {
                url: '{!! route('support-team.index') !!}',
                data: function(data) {

                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'user.name', 
                    name: 'user.name', 
                },
                { 
                    data: 'get_notifications', 
                    name: 'get_notifications', 
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false
                }
            ], columnDefs: [
                {
                    targets: -1,
                    render: function (data, type, row, meta) {
                        if (type === 'display') {
                            var actions = '';
                            @if (can('edit', 'support-team'))
                                var url = "{{ route('support-team.edit',':id') }}";
                                url = url.replace(':id', row.id);
                                actions += `<a href="`+url+`" title="view"><i class="fa fa-solid fa-pencil"></i></a>`;
                            @endif
                            @if (can('delete', 'support-team'))
                                actions += `<a onclick="deleteFn(`+row.id+`,'`+row.user.name+`')" title="delete" style="margin-left:5px;cursor:pointer;color:red;"><i class="fas fa-trash-alt"></i></a>`;
                            @endif
                            return actions;
                        }
                        return data;
                    }
                }
            ],
        });

        $('#confirmDeleteBtn').on('click', function (e) {
                e.preventDefault();           
                
                var url = "{{ route('support-team.delete',':id') }}";
                url = url.replace(':id', $(this).data('id'));
                window.location.href = url;
            });

    });

    function deleteFn(id,name){
        $('#confirmDeleteBtn').data('id',id);
        $('#deleteBody').text('Are you sure you want to delete, '+name);
        $('#deleteModal').modal('show');
    }
    </script>
@endsection