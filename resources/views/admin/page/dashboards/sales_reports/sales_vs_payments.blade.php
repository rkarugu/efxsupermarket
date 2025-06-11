<div class="col-md-8 dashboard-card">
    <div id="sales_vs_payments_perfomance"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Chart data from Laravel
    const chartData = @json($chartData);

    // Chart options
    const options = {
        series: [
            {
                name: 'Sales',
                data: chartData.sales
            },
            {
                name: 'Payments',
                data: chartData.payments
            }
        ],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: chartData.months,
        },
        yaxis: {
            title: {
                text: 'Amount'
            }
        },
        fill: {
            opacity: 1
        },
        colors: ['#2196F3', '#008000'], // Sales (Blue) and Payments (Green)
        tooltip: {
            y: {
                formatter: function (val) {

                    return "Kes " + formatNumberWithCommas(val.toFixed(2));
                }
            }
        }
    };

    // Render chart
    const chart = new ApexCharts(document.querySelector("#sales_vs_payments_perfomance"), options);
    chart.render();

            function formatNumberWithCommas(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthlySalesData = @json($salesPerMonth);
        const monthlyPaymentsData = @json($paymentsPerMonth);

        const monthlySales = {};
        const monthlyPayments = {};
        monthlySalesData.forEach(item => {
            monthlySales[item.month] = parseFloat((item.total_sales).toFixed(2));
        });

        monthlyPaymentsData.forEach(item => {
            monthlyPayments[item.month] = parseFloat((item.total_amount).toFixed(2));
        });

        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1;
        const desiredMonths = [];
        for (let month = 3; month <= currentMonth; month++) {
            const monthString = month < 10 ? `0${month}` : month;
            desiredMonths.push(`${currentYear}-${monthString}`);
        }

        const allMonths = new Set([...Object.keys(monthlySales), ...Object.keys(monthlyPayments)]);
        const labels = Array.from(allMonths).sort().filter(month => desiredMonths.includes(month));

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
            'September', 'October', 'November', 'December'
        ];
        const labelsFormatted = labels.map(month => monthNames[parseInt(month.split('-')[1]) - 1]);

        const salesData = labels.map(month => monthlySales[month] || 0);
        const paymentsData = labels.map(month => monthlyPayments[month] || 0);

        function formatNumberWithCommas(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function formatNumberWithScale(number) {
            if (Math.abs(number) >= 1.0e+9) {
                return (number / 1.0e+9).toFixed(2);
            } else if (Math.abs(number) >= 1.0e+6) {
                return (number / 1.0e+6).toFixed(2);
            } else if (Math.abs(number) >= 1.0e+3) {
                return (number / 1.0e+3).toFixed(2);
            } else {
                return number.toFixed(2);
            }
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
            colors: ['#2196F3', '#008000'],
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
                name: 'Sales',
                data: salesData
            }, {
                name: 'Payments',
                data: paymentsData
            }],
            xaxis: {
                categories: labelsFormatted,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Amount (Kes) in millions',
                    style: {
                        fontSize: '14px'
                    }
                },
                labels: {
                    formatter: function(value) {
                        return formatNumberWithScale(value);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "Kes " + formatNumberWithCommas(val.toFixed(2));
                    }
                }
            },
            title: {
                text: 'Sales vs Payments (Monthly Performance)',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#sales_vs_payments_perfomance"), options);
        chart.render();
    });
</script>
