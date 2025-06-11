@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $category }} Supplier Sales </h3>
                </div>
            </div>

            <div class="box-body">
                <form action="" method="get">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <div class="form-group col-sm-3">
                        <label for="from">From</label>
                        <input type="date" name="startDate" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="form-group col-sm-3" style="margin-inline: 10px">
                        <label for="to">To</label>
                        <input type="date" name="endDate" class="form-control" value="{{ $endDate }}">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top: 25px;">Filter</button>
                    <a href="{{route('suppliers-overview.suppliers-sales-by-category', ['category' => $category])}}" class="btn btn-primary" style="margin-top:25px;">Clear</a>
                    <a href="{{ route('suppliers-overview.suppliers-sales-by-category-print', ['category' => $category, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-primary" style="float: right">Print</a>
                </form>

                <div style="clear:both;">
                    <hr>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <table class="table table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Supplier Name</th>
                                    <th style="text-align: right">Total Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesData as $i => $saleData)
                                    <tr>
                                        <th>{{ ++$i }}</th>
                                        <td>{{ $saleData->supplier_name }}</td>
                                        <td style="text-align: right">{{ number_format($saleData->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" style="text-align: right">Total</th>
                                    <th style="text-align: right">{{ number_format($salesData->sum('amount')) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-7" id="chart"></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script src="{{ asset('js/utils.js') }}"></script>
    
    <script>
        $('.datatable').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'info': false,
            'autoWidth': false,
            'pageLength': 10,
            'initComplete': function (settings, json) {
                let info = this.api().page.info();
                let total_record = info.recordsTotal;
                if (total_record < 11) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }],
        });

        const salesData = {!! $salesData !!}

        const suppliers = salesData.map(saleData => saleData.supplier_name)
        const series = salesData.map(saleData => saleData.amount)

        var options = {
            series: [{
                data: series,
                name: 'Sales'
            }],
            chart: {
                type: 'bar',
                height: 'auto',
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: suppliers,
                labels: {
                    formatter: function (val) {
                        return numberWithCommas(val)
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return `KES ${numberWithCommas(val)}`
                    },
                },
            },
        };

        const chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endpush


