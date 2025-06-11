<div class="col-md-8 dashboard-card">
    <canvas id="total_debtor_balances_perfomance"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // sales, revenue and returns chart start
        const ctx = document.getElementById('total_debtor_balances_perfomance');

        const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
            'October', 'November', 'December'
        ];

        const dataset1 = {
            label: 'Tonnage',
            data: [65, 59, 80, 81, 56, 55, 40, 30, 20, 10, 5, 15],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            stack: 'combined',
            type: 'bar'
        };

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [dataset1]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Total Debtor Balances (Monthly)'
                    }
                },
                scales: {
                    y: {
                        stacked: true
                    }
                }
            }
        });
        // sales, revenue and returns chart end

    });
</script>
