@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Unsigned Invoices Report</h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="filterForm"
                      action="{{ route('sales-and-receivables-reports.unassigned_invoices', ['route_id' => request()->route_id, 'start_date' => request()->start_date, 'end_date' => request()->end_date]) }}"
                      method="get">

                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="" class="control-label"> Start Date </label>
                            <input type="date" name="start_date"
                                   value="{{ request()->start_date ?? \Carbon\Carbon::now()->toDateString() }}"
                                   class="form-control"/>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="" class="control-label"> End Date </label>
                            <input type="date" name="end_date"
                                   value="{{ request()->end_date ?? \Carbon\Carbon::now()->toDateString() }}"
                                   class="form-control"/>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="route" class="control-label"> Select Route </label>
                            <select name="route" id="route" class="form-control">
                                <option value="" selected disabled> Select a route</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}" @if (request()->route == $route->id) selected @endif>
                                        {{ $route->route_name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary"/>
                            <input type="submit" name="intent" value="EXCEL" class="btn btn-primary ml-12"/>
                            <input type="submit" name="intent" value="RE-SIGN" class="btn btn-primary ml-12"/>
                            <input type="submit" name="intent" value="CLEAR" class="btn btn-primary ml-12" onclick="clearForm()"/>

                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="unassigned-invoices-table">
                        <thead>
                        <tr>
                            <th>Invoice Date</th>
                            <th>Invoice Number</th>
                            <th>Route</th>
                            <th>Invoice Total</th>
                            <th>Tax Total</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($combinedData as $data)
                            <tr>
                                <td>{{ $data->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a
                                            href="{{ route('sales-and-receivables-reports.filter-unassigned-invoices-items', ['invoice_number' => $data->requisition_no, 'start_date' => request()->start_date, 'end_date' => request()->end_date]) }}"
                                            target="_blank"
                                    >
                                        {{ $data->requisition_no }}
                                    </a>
                                </td>
                                <td>{{ $data->route }}</td>
                                <td style="text-align: right">{{ number_format($data->get_related_item_sum_total_cost_with_vat, 2) }}</td>
                                <td style="text-align: right">{{ number_format($data->get_related_item_sum_vat_amount, 2) }}</td>
                                <td>{{ $data->description }}</td>
                                <td>
                                    @if( (isset($user->permissions['sales-invoice___confirm-invoice-r'])) || $user->role_id == '1')
                                        <a style="margin: 2px; background:#063970; color:#fff;" class="btn btn-sm"
                                           href="{{ route('transfers.invoice-resign-esd',base64_encode($data->id)) }}'" target="_blank" title="Re-Sign ESD">
                                            <i class="fa fa-repeat" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script type="text/javascript">
        $(function () {
            $('body').addClass('sidebar-collapse');
            $("#route").select2();
            $("#filter").select2();
            $("#group").select2();
        });

        $(document).ready(function () {
            var ctnsDznsValue = '{{ request()->input('ctns_dzns') }}';
            $('input[name="ctns_dzns"]').val(ctnsDznsValue);
            var routeId = '{{ request()->input('route_id') }}';
            $('input[name="route_id"]').val(routeId);
        });

        function clearForm() {
            document.getElementsByName('start_date')[0].valueAsDate = new Date();
            document.getElementsByName('end_date')[0].valueAsDate = new Date();

            document.getElementById('route').value = '';

            document.getElementsByName('ctns_dzns')[0].value = ctnsDznsValue;
            document.getElementsByName('route_id')[0].value = routeId;

            document.getElementById('filterForm').submit();
        }

        $(document).ready(function () {
            $('#unassigned-invoices-table').DataTable({
                "paging": true,
                "pageLength": 100,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
        });
    </script>
@endsection
