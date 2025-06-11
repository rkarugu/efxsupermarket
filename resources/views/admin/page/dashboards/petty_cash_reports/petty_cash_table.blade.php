<div class="col-md-8 dashboard-card">
    <h4 style="font-weight: bolder;text-align:center">Petty Cash Transactions</h4>
    <div class="row" style="margin-bottom: 10px">
        <div class="col-md-6 col-md-offset-6 text-right">
            <form id="filterForm" action="{{ route('chair-petty-cash-reports.index') }}" method="GET" class="form-inline"
                role="form">
                <div class="form-group">
                    <label for="year" style="font-size: 12px; display: block; text-align: left;">Year:</label>
                    <select name="year" id="year" class="form-control input-sm">
                        @for ($i = 2020; $i <= \Carbon\Carbon::now()->year; $i++)
                            <option value="{{ $i }}"
                                {{ $i == request('year', \Carbon\Carbon::now()->year) ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label for="month" style="font-size: 12px; display: block; text-align: left;">Month:</label>
                    <select name="month" id="month" class="form-control input-sm">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}"
                                {{ $i == request('month', \Carbon\Carbon::now()->month) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <button type="submit" style="margin-top: 22px;" class="btn btn-sm btn-primary">
                    <i class="fa fa-filter"></i> Filter
                </button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table id="example" class="display">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Order Taking</th>
                    <th>Delivery</th>
                    <th style="text-align: right !important">Totals</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_order_taking_amount = 0;
                    $total_delivery_amount = 0;
                @endphp
                @foreach ($merged_data as $date => $count)
                    @php
                        $total_order_taking_amount += $count['order_taking']['total_amount'];
                        $total_delivery_amount += $count['delivery']['total_amount'];
                    @endphp
                    <tr>
                        <td>{{ $date }}</td>
                        <td class="order-taking-cell">
                            {{ $count['order_taking']['count'] }}<br>
                            <div class="order-taking-inner"></div>
                            {{ number_format($count['order_taking']['total_amount'], 2) }}
                        </td>
                        <td class="order-taking-cell">
                            {{ $count['delivery']['count'] }}<br>
                            <div class="order-taking-inner"></div>
                            {{ number_format($count['delivery']['total_amount'], 2) }}
                        </td>
                        <td class="order-taking-cell" style="text-align: right !important">
                            <br>
                            <div class="order-taking-inner"></div>
                            {{ number_format($count['order_taking']['total_amount'] + $count['delivery']['total_amount'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Grand Totals</strong></td>
                    <td><strong>{{ number_format($total_order_taking_amount, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_delivery_amount, 2) }}</strong></td>
                    <td style="text-align: right !important">
                        <strong>{{ number_format($total_order_taking_amount + $total_delivery_amount, 2) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


<style>
    .wrapper {
        margin: 20px;
        font-family: sans-serif;
    }

    .datatable td,
    .datatable th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .order-taking-cell {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }

    .order-taking-inner {
        padding-bottom: 10px;
    }

    .order-taking-inner {
        left: 0;
        right: 0;
        width: 100%;
        border-bottom: 1px solid #ccc;
    }

    ul,
    li {
        padding-left: 5px;
        margin-left: 5px;
    }
</style>

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#example').DataTable();

            $('#example tbody').on('click', '.extra-row-trigger', function() {
                var $tr = $(this).closest('tr');
                var $nextRow = $tr.next('tr');

                $nextRow.toggle();
            });

            $('#example').addClass('datatable');

            table.on('draw', function() {
                var $table = $(this);

                $table.find("tbody>tr").each(function() {
                    var $tr = $(this);

                    var extra_row = $tr.find(".extra-row-content").html();

                    if (!$tr.next().hasClass('dt-added')) {
                        $tr.after(extra_row);
                        $tr.find("td").each(function() {
                            var $td = $(this);
                            var rowspan = parseInt($td.data("datatable-multi-row-rowspan"),
                                10);
                            if (rowspan) {
                                $td.attr('rowspan', rowspan);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
