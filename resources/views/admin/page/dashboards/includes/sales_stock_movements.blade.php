<div class="card-content">
    <canvas id="salesStockChart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // sales and stock movement chart start
        const ctx = document.getElementById('salesStockChart');

        const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
            'October', 'November', 'December'
        ];

        const salesDataset = {
            label: 'Sales',
            data: [65, 59, 80, 81, 56, 55, 40, 30, 20, 10, 5, 15],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: false,
            tension: 0.1
        };

        const stockMovementDataset = {
            label: 'Stock Movement',
            data: [28, 48, 40, 19, 86, 27, 90, 50, 70, 45, 60, 35],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: false,
            tension: 0.1
        };

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [salesDataset, stockMovementDataset]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Sales vs Stock Movement'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        // sales and stock movement chart end
    });
</script>
