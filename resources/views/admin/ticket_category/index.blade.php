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
                        <a href="{{ route('ticket-category.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Category</a>
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
                                <th>Action</th>
                            </tr>
                        </thead>                        
                    </table>
                </div>
            </div>
        </div>
    </section>
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
                url: '{!! route('ticket-category.index') !!}',
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
                    data: 'title', 
                    name: 'title', 
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
                            @if (can('show', 'ticket-category'))
                                var url = "{{ route('ticket-category.edit',':id') }}";
                                url = url.replace(':id', row.id);
                                actions += `<a href="`+url+`" title="view"><i class="fa fa-solid fa-pencil"></i></a>`;
                            @endif
                            return actions;
                        }
                        return data;
                    }
                }
            ],
        });
    });

        
    </script>
@endsection