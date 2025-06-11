<div class="col-md-8 dashboard-card">
    <div id="summarized_route_perfomance_report"></div>
</div>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        const data = @json($salesPerMonth);


        function monthStringToName(monthStr) {
            const date = new Date(monthStr + "-01");
            const options = { year: 'numeric', month: 'long' };
            return date.toLocaleDateString('en-US', options);
        }
        data.sort((a, b) => new Date(a.month) - new Date(b.month));
        const transformedData = data.map(item => ({
            total_sales: item.total_sales,
            month: monthStringToName(item.month)
        }));

        const months = transformedData.map(item => item.month);
        const sales = transformedData.map(item => item.total_sales);

        function formatNumberWithCommas(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        const options = {
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            colors: ['#2196F3'],
            series: [{
                name: 'Amount',
                data: sales
            }],
            xaxis: {
                categories: months,
                title: {
                    text: 'Month',
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
                min: 0,
                title: {
                    text: 'Amount (Kes) in millions',
                    style: {
                        fontSize: '14px'
                    }
                },
                labels: {
                    formatter: function(value) {
                        if (value >= 1000000) {
                            return (value / 1000000).toFixed(0);
                        } else {
                            return parseFloat(value).toFixed(2);
                        }
                    }
                }
            },
            title: {
                text: 'Summarized Monthly Performance',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#summarized_route_perfomance_report"), options);
        chart.render();
    });
</script>
