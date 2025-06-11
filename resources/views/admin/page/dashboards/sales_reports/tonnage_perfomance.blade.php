<div class="col-md-8 dashboard-card">
    <div id="tonnage_performance"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const data = @json($tonnagePerMonth);
        function monthStringToName(monthStr) {
            const date = new Date(monthStr + "-01");
            const options = { year: 'numeric', month: 'long' };
            return date.toLocaleDateString('en-US', options);
        }
        data.sort((a, b) => new Date(a.month) - new Date(b.month));
        const transformedData = data.map(item => ({
            total_tonnage: item.total_tonnage,
            month: monthStringToName(item.month)
        }));

        const months = transformedData.map(item => item.month);
        const tonnages = transformedData.map(item => item.total_tonnage);

        function formatNumberWithCommas(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        var options = {
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'rounded'
                },
            },
            colors: ['#2196F3'],
            series: [{
                name: 'Tonnage',
                data: tonnages
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
                title: {
                    text: 'Tonnage',
                    style: {
                        fontSize: '14px'
                    }
                }
            },
            title: {
                text: 'Monthly Tonnage Performance',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return formatNumberWithCommas(val.toFixed(2))
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#tonnage_performance"), options);
        chart.render();
    });
</script>
