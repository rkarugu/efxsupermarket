@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="box-header-flex">
                    <h3 class="box-title">Customer Accounts</h3>
                    <div>
                        @if (can('add', $model))
                            <a href="{!! route($model . '.create') !!}" class="btn btn-success">
                                <i class="fa fa-plus"></i>
                                Add {!! $title !!}
                            </a>
                        @endif
                        @if (can('edit', $model))
                            <a href="{!! route($model . '.index', ['enable_all' => true]) !!}" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i>
                                Enable All {!! $title !!}
                            </a>
                        @endcan
                        @if (can('edit', $model))
                            <a href="{!! route($model . '.index', ['disable_all' => true]) !!}" class="btn btn-primary">
                                <i class="fa fa-exclamation-circle"></i>
                                Disable All {!! $title !!}
                            </a>
                        @endcan
                        <a href="{!! route($model . '.index', ['download' => true]) !!}" class="btn btn-primary">
                            <i class="fa fa-file-excel"></i>
                            Export Excel
                        </a>
            </div>
        </div>
    </div>
    <div class="box-body">
        {!! Form::open(['route' => 'maintain-customers.index', 'method' => 'get']) !!}
        <div class="row">
            <div class="col-md-3 form-group">
                <select name="branch" id="branch" class="form-control mlselect" @disabled(!can('view_all_branches_data', 'employees'))>
                    <option value="" selected disabled>Select branch</option>
                    @foreach ($branches as $branch)
                        {{-- <option value="{{$branch->id}}" {{ $branch->id == request()->branch ? 'selected' : '' }}>{{$branch->name}}</option> --}}
                        <option value="{{ $branch->id }}"
                            {{ request()->has('branch') ? ($branch->id == request()->branch ? 'selected' : '') : ($branch->id == $user->restaurant_id ? 'selected' : '') }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                <a class="btn btn-success ml-12" href="{!! route('maintain-customers.index') !!}">Clear </a>
            </div>
        </div>

        {!! Form::close() !!}
        <table class="table table-bordered table-hover" id="customersDataTable">
            <thead>
                <tr>
                    <th>Customer Code</th>
                    <th>Customer Name</th>
                    <th>Route</th>
                    <th>Equity Till</th>
                    <th>KCB Till</th>
                    <th>Is-Blocked</th>
                    <th>Amount</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th class="text-right" colspan="6">Grand Total</th>
                    <th id="total" class="text-right"></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</section>
@endsection
@section('uniquepagestyle')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection
@push('scripts')
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $(".mlselect").select2();
    });
</script>
<script>
    $(function() {
        let table = $("#customersDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('maintain-customers.index') !!}',
                data: function(data) {
                    data.branch = $('#branch').val();
                }
            },
            columns: [{
                    data: "customer_code",
                    name: "customer_code",
                },
                {
                    data: "customer_name",
                    name: "customer_name",
                },
                {
                    data: "route_name",
                    name: "routes.route_name",
                },
                {
                    data: "equity_till",
                    name: "equity_till",
                },
                {
                    data: "kcb_till",
                    name: "kcb_till",
                },
                {
                    data: "is_blocked",
                    name: "is_blocked",
                },
                {
                    data: "balance",
                    name: "balance",
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "actions",
                    name: "actions",
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                },
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var json = api.ajax.json();

                $("#total").html(Number(json.total).formatMoney());
            }
        });

        table.on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });
</script>
@endpush
