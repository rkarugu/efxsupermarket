@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $title }}</h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div>
                            <label for="Bin">Bin Location</label>
                            <select name="bin" id="bin" class="select2 form-control">
                                <option value="">Choose Bin</option>
                                @foreach (getUnitOfMeasureList() as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div>
                            <label for="date">Dispatch Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="">
                        </div>
                    </div>
                </div>
                <div>
                    <hr>
                </div>
                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Dispatch Date</th>
                                <th>Dispatch Time</th>
                                <th>Dispatcher</th>
                                <th>Bin</th>
                                <th>Route</th>
                                <th>Item Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                
                        </tbody>                        
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
@endsection
@section('uniquepagescript')
<script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#bin,#date').on('change', function() {
            $("#dataTable").DataTable().ajax.reload();
        });

        let table = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('small-packs.dispatched') !!}',
                data: function(data) {
                    data.role = $("#bin").val();
                    data.date = $("#date").val();
                }
            },
            columns: [
                {
                    data: "dispatch_date",
                    name: "dispatch_date",
                },
                {
                    data: "dispatch_time",
                    name: "dispatch_time",
                },
                {
                    data: "dispatched_by.name",
                    name: "dispatchedBy.name",
                },
                {
                    data: "uom.title",
                    name: "uom.title",
                },
                {
                    data: "route_name",
                    name: "route_name"
                },
                {
                    data: "item_count",
                    name: "item_count",
                },                
                {
                        data: null,
                        orderable: false,
                        searchable: false
                }
            ],
            columnDefs: [
                    
                    {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '<div class="d-flex">';
                                    var actionUrl = "{{ route('small-packs.dispatched-view', 'id') }}";
                                    actionUrl = actionUrl.replace('id', row.id);
                                    actions += `<a href="`+actionUrl+`" role="button" title="View"><i class="fa fa-solid fa-eye"></i></a>`;
                                actions +='</div>';
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