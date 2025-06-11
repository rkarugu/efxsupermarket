<div class="col-md-8 dashboard-card">
    <div id="met_perfomance"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataFromBackend = @json($monthly_met_unmet_data);

        const labels = [];
        const metData = [];
        const unmetData = [];
        const onsiteData = [];
        const offsiteData = [];
        const metWithoutOrdersData = [];
        const shopsCountData = [];

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August",
            "September", "October", "November", "December"
        ];

        dataFromBackend.forEach(item => {
            const [year, month] = item.month.split('-');
            labels.push(monthNames[parseInt(month) - 1]);
            metData.push(parseInt(item.total_met));
            unmetData.push(parseInt(item.total_unmet));
            onsiteData.push(parseInt(item.onsite));
            offsiteData.push(parseInt(item.offsite));
            metWithoutOrdersData.push(parseInt(item.met_without_orders));
            shopsCountData.push(parseInt(item.shops_count));
        });

        function formatNumberWithCommas(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        const options = {
            chart: {
                type: 'line',
                height: 350,
                stacked: false,
                toolbar: {
                    show: false
                }
            },
            colors: ['#2196F3', '#4CAF50', '#FF6347', '#9966FF', '#FF9F40', '#000000'],
            series: [{
                    name: 'Met',
                    data: metData
                },
                {
                    name: 'Unmet',
                    data: unmetData
                },
                {
                    name: 'Onsite',
                    data: onsiteData
                },
                {
                    name: 'Offsite',
                    data: offsiteData
                },
                {
                    name: 'Met Without Orders',
                    data: metWithoutOrdersData
                },
                {
                    name: 'Total Shops Count',
                    data: shopsCountData
                }
            ],
            xaxis: {
                categories: labels,
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
                    text: 'Customers in thousands',
                    style: {
                        fontSize: '14px'
                    }
                }
            },
            title: {
                text: 'Met Monthly Performance',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return formatNumberWithCommas(val.toFixed(2))
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#met_perfomance"), options);
        chart.render();
    });
</script>
