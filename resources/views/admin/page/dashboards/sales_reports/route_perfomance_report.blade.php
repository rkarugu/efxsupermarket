<div class="col-md-8 dashboard-card">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 style="font-weight: bolder">Route Sales Perfomance</h4>
        </div>
        <div class="col-md-6 col-md-offset-6 text-right">
            <a href="{{ route('salesman-performance-report', ['branch_id' => request()->branch_id]) }}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fas fa-book-open"></i>
                Detailed Route Performance
            </a>
        </div>
    </div>


    <div class="table-responsive" style="margin-top: 10px !important;">
        <table class="table table-bordered table-hover" id="route-cumulative-performance-table">
            <thead>
            <tr id="route-cumulative-performance-header">
                <th>Routes</th>
            </tr>
            </thead>
            <tbody id="route-cumulative-performance-body">
            </tbody>
            <tfoot>
            <tr id="route-cumulative-performance-footer" style="font-weight:bolder">
                <td>Grand Total</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_red.css">
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <script type="text/javascript">
        $(function() {
            $("#route").select2();
            $("#filter").select2();
            $("#group").select2();
            $(".new_filters").select2();
            $(".branch_filter").select2();
        });

        $(document).ready(function() {
            var data = @json($routeMonthlySales);
            console.log(data)

            var months = [];
            for (var route in data) {
                for (var month in data[route]) {
                    if (!months.includes(month)) {
                        months.push(month);
                    }
                }
            }

            months.sort();

            function formatMonth(dateString) {
                var date = new Date(dateString + '-01');
                return date.toLocaleString('default', {
                    month: 'long'
                });
            }

            var headerRow = $("#route-cumulative-performance-header");
            months.forEach(function(month) {
                headerRow.append('<th>' + formatMonth(month) + '</th>');
            });
            headerRow.append('<th>Grand Total</th>');

            var body = $("#route-cumulative-performance-body");
            for (var route in data) {
                var row = '<tr><td>' + route + '</td>';
                var grandTotal = 0;
                months.forEach(function(month) {
                    var amount = data[route][month] ? data[route][month] : '0.00';
                    grandTotal += parseFloat(amount);
                    row += '<td>' + parseFloat(amount).toLocaleString() + '</td>';
                });
                row += '<td>' + grandTotal.toLocaleString() + '</td>';
                row += '</tr>';
                body.append(row);
            }

            var totals = {};
            var grandTotalAllRoutes = 0;
            months.forEach(function(month) {
                totals[month] = 0;
            });

            for (var route in data) {
                var routeGrandTotal = 0;
                months.forEach(function(month) {
                    if (data[route][month]) {
                        totals[month] += parseFloat(data[route][month]);
                        routeGrandTotal += parseFloat(data[route][month]);
                    }
                });
                grandTotalAllRoutes += routeGrandTotal;
            }

            var footerRow = $("#route-cumulative-performance-footer");
            months.forEach(function(month) {
                footerRow.append('<td>' + totals[month].toLocaleString() + '</td>');
            });
            footerRow.append('<td>' + grandTotalAllRoutes.toLocaleString() + '</td>');

            $('#route-cumulative-performance-table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
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
