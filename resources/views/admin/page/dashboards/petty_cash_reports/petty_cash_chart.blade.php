<div class="col-md-8 dashboard-card">
    <h4 style="font-weight: bolder;text-align:center">Daily Order Taking and Delivery Performance</h4>
    <div class="row" style="margin-bottom: 10px">
        <div class="col-md-6 col-md-offset-6 text-right">
            <form id="filterForm" action="{{ route('chair-petty-cash-reports.index') }}" method="GET" class="form-inline"
                role="form">
                <div class="form-group">
                    <label for="year" style="font-size: 12px; display: block; text-align: left;">Year:</label>
                    <select name="year" id="year" class="form-control input-sm">
                        @for ($i = 2023; $i <= \Carbon\Carbon::now()->year; $i++)
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

    <div id="petty_cash_performance"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const data = @json($merged_data);
        const selectedYear = document.getElementById('year').value;
        const selectedMonth = document.getElementById('month').value;

        const daysInMonth = new Date(selectedYear, selectedMonth, 0).getDate();
        const labels = Array.from({
            length: daysInMonth
        }, (_, i) => i + 1);

        const order_taking_amount_data = [];
        const delivery_amount_data = [];

        labels.forEach(day => {
            const date =
                `${selectedYear}-${selectedMonth.padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
            const order_taking_total = data[date] && data[date]['order_taking'] ? parseFloat(data[date][
                'order_taking'
            ]['total_amount']) : 0;
            const delivery_total = data[date] && data[date]['delivery'] ? parseFloat(data[date][
                'delivery'
            ]['total_amount']) : 0;

            order_taking_amount_data.push(order_taking_total);
            delivery_amount_data.push(delivery_total);
        });

        function formatNumberWithCommas(number) {
            return number.toLocaleString('en-US', {
                maximumFractionDigits: 2
            });
        }

        var options = {
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
                toolbar: {
                    show: false
                }
            },
            colors: ['#FF6347', '#9966FF'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Order Taking Amount',
                data: order_taking_amount_data
            }, {
                name: 'Delivery Amount',
                data: delivery_amount_data
            }],
            xaxis: {
                categories: labels,
                title: {
                    text: 'Day',
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
            yaxis: {
                title: {
                    text: 'Amount (in thousands)',
                    style: {
                        fontSize: '14px'
                    }
                },
                labels: {
                    formatter: function(value) {
                        return formatNumberWithCommas(value);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return formatNumberWithCommas(val);
                    }
                }
            },

        };

        var chart = new ApexCharts(document.querySelector("#petty_cash_performance"), options);
        chart.render();
    });
</script>
