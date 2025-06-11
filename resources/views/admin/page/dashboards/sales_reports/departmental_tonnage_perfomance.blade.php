<div class="col-md-8 dashboard-card">
    <div class="row">
        <div class="col-md-6 col-md-offset-6 text-right">
            <form id="filterForm" action="{{ route('chair-sales-reports.index') }}" method="GET" class="form-inline"
                  role="form">
                <input type="hidden" name="branch_id" value="{{ Request()->branch_id }}">
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

    @if (!empty($categories))
        <div id="departmental_tonnage_performance"></div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categories = @json($categories);

                const labels = categories.map(category => category.category_description);
                const data = categories.map(category => parseFloat(category.total_tonnage));

                const options = {
                    chart: {
                        type: 'bar',
                        height: labels.length * 20,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Total Tonnage',
                        data: data
                    }],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '50%',
                            endingShape: 'rounded'
                        }
                    },
                    xaxis: {
                        categories: labels,
                        title: {
                            text: 'Total Tonnage',
                            style: {
                                fontSize: '14px'
                            }
                        },
                        labels: {
                            formatter: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Department',
                            style: {
                                fontSize: '14px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    title: {
                        text: 'Departmental Tonnage Performance (Current Month)',
                        align: 'center',
                        style: {
                            fontSize: '16px',
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return `Total Tonnage: ${val.toLocaleString()}`;
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    responsive: [{
                        breakpoint: 600,
                        options: {
                            plotOptions: {
                                bar: {
                                    horizontal: false
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                var chart = new ApexCharts(document.querySelector("#departmental_tonnage_performance"), options);
                chart.render();
            });
        </script>
    @endif
</div>
