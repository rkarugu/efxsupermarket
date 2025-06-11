<div class="col-md-4 dashboard-card">
    <canvas id="branchesChart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // branch perfomance start

        const ctxBranches = document.getElementById('branchesChart');

        const branchData = [{
                branch: 'THIKA',
                averageSales: 20,
                averageReturns: 10,
                averageRevenue: 30,
                totalBins: 100,
                totalRoutes: 50,
                totalCustomers: 500
            },
            {
                branch: 'NAIVASHA',
                averageSales: 15,
                averageReturns: 5,
                averageRevenue: 25,
                totalBins: 80,
                totalRoutes: 40,
                totalCustomers: 450
            },
            {
                branch: 'NAKURU',
                averageSales: 30,
                averageReturns: 15,
                averageRevenue: 45,
                totalBins: 120,
                totalRoutes: 60,
                totalCustomers: 600
            },
            {
                branch: 'ENGINEER',
                averageSales: 25,
                averageReturns: 12,
                averageRevenue: 35,
                totalBins: 110,
                totalRoutes: 55,
                totalCustomers: 550
            },
            {
                branch: 'MERU',
                averageSales: 18,
                averageReturns: 8,
                averageRevenue: 28,
                totalBins: 90,
                totalRoutes: 45,
                totalCustomers: 480
            },
            {
                branch: 'NAROK',
                averageSales: 22,
                averageReturns: 9,
                averageRevenue: 32,
                totalBins: 95,
                totalRoutes: 48,
                totalCustomers: 520
            },
            {
                branch: 'MAUA',
                averageSales: 17,
                averageReturns: 7,
                averageRevenue: 27,
                totalBins: 85,
                totalRoutes: 42,
                totalCustomers: 430
            },
            {
                branch: 'NYAHURURU',
                averageSales: 28,
                averageReturns: 13,
                averageRevenue: 38,
                totalBins: 115,
                totalRoutes: 58,
                totalCustomers: 580
            },
            {
                branch: 'KARATINA',
                averageSales: 23,
                averageReturns: 11,
                averageRevenue: 33,
                totalBins: 105,
                totalRoutes: 53,
                totalCustomers: 560
            },
            {
                branch: 'THIKA STORE (Nampak)',
                averageSales: 19,
                averageReturns: 6,
                averageRevenue: 29,
                totalBins: 88,
                totalRoutes: 44,
                totalCustomers: 470
            }
        ];

        const branchLabels = branchData.map(data => data.branch);
        const branchAverages = branchData.map(data => ({
            sales: data.averageSales,
            returns: data.averageReturns,
            revenue: data.averageRevenue
        }));

        new Chart(ctxBranches, {
            type: 'bar',
            data: {
                labels: branchLabels,
                datasets: [{
                    label: 'Number of Stores',
                    data: branchAverages.map(avg => avg
                        .sales),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItem) {
                                return branchData[tooltipItem[0].dataIndex].branch;
                            },
                            label: function(tooltipItem) {
                                const data = branchData[tooltipItem.dataIndex];
                                return [
                                    `Sales: ${data.averageSales}`,
                                    `Returns: ${data.averageReturns}`,
                                    `Revenue: ${data.averageRevenue}`,
                                    `Total Bins: ${data.totalBins}`,
                                    `Total Routes: ${data.totalRoutes}`,
                                    `Total Customers: ${data.totalCustomers}`
                                ];
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Branch Average Performance (Monthly)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Percentage (%)'
                        }
                    },
                    // x: {
                    //     title: {
                    //         display: true,
                    //         text: 'Branch Names'
                    //     }
                    // }
                }
            }
        });

        // branch perfomance end
    });
</script>
