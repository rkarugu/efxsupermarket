@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{!! $title !!}</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a>
                </div>
            </div>

            <div class="box-body">

                <form action="{{ route('reports.items_data_purchase_report') }}" method="get">
                    <input type="hidden" id="startDate" name="from">
                    <input type="hidden" id="endDate" name="to">
                    <input type="hidden" id="action" name="action">

                    <div class="row">
                        <div class="col-sm-3">
                            <div id="reportRange" class="reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control" name="supplier" id="supplier">
                                <option value="">Select Option</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary" name="action" value="excel">
                                <i class="fa fa-file-alt"></i> Excel
                            </button>
                            <!--  <button type="submit" class="btn btn-primary" name="action" id="generatePDFBtn">
                                    <i class="fa fa-file"></i> PDF
                                </button> -->

                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered" id="averageSalesDataTable">
                    <thead>
                        <tr style="font-size: 12px">

                            <th>ITEM CODE</th>
                            <th>ITEM NAME</th>
                            @foreach ($locations as $location)
                                <th>{{ $location->location_name }}</th>
                            @endforeach
                            <th>TOTALS</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
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
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function(e) {
            $('.mtselect').select2();
        });
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $("body").addClass('sidebar-collapse');



        $(document).ready(function() {

            $("#supplier").select2()
            $("#supplier").change(function() {
                refreshTable();
            })

            let start = moment().startOf('month');
            let end = moment().endOf('month');

            $("#startDate").val(start.format('YYYY-MM-DD'));
            $("#endDate").val(end.format('YYYY-MM-DD'));

            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });

            $('.reportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#startDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#endDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable();
            });

            $("#averageSalesDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "asc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('reports.items_data_purchase_report') !!}',
                    data: function(data) {
                        data.from = $("#startDate").val();
                        data.to = $("#endDate").val();
                        data.supplier = $("#supplier").val();
                    }
                },
                columns: [{
                        data: 'stock_id_code',
                        name: 'stock_id_code'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    @foreach ($locations as $location)
                        {
                            data: 'total_item_sales_{{ $location->id }}',
                            name: 'total_item_sales_{{ $location->id }}',
                            orderable: false,
                            searchable: false
                        },
                    @endforeach

                    {
                        data: 'total_sales_all_locations',
                        name: 'total_sales_all_locations',
                        orderable: false,
                        searchable: false
                    },

                ]
            })
        })


        function generateExcel() {
            var table = $("#averageSalesDataTable").DataTable();

            table.one('xhr', function(e, settings, json) {
                var tableData = table.rows().data();
                var excelData = [];
                var headers = ['ITEM CODE', 'ITEM NAME'];
                @foreach ($locations as $location)
                    headers.push('{{ $location->location_name }}');
                @endforeach
                excelData.push(headers);
                tableData.each(function(row) {
                    var rowData = [];
                    rowData.push(row.stock_id_code, row.title);
                    @foreach ($locations as $location)
                        rowData.push(row['total_item_sales_{{ $location->id }}']);
                    @endforeach
                    excelData.push(rowData);
                });


                var csvContent = "data:text/csv;charset=utf-8," +
                    excelData.map(e => e.join(",")).join("\n");
                var encodedUri = encodeURI(csvContent);
                var link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "item_sales_report.xlsx");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }).ajax.reload();
        }

        $("#ExcelBtn").click(function() {
            generateExcel();
        });


        function refreshTable() {
            $("#averageSalesDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
