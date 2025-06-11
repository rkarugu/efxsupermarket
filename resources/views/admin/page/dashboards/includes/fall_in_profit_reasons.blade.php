<div class="card-content">
    <canvas id="reasonsForFallInProfitChart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Doughnut chart for reasons for fall in profit
        const ctxProfit = document.getElementById('reasonsForFallInProfitChart').getContext('2d');

        const reasonsForFallInProfitData = {
            labels: ['Increased Costs', 'Decreased Sales', 'Market Competition', 'Poor Marketing', 'Other'],
            datasets: [{
                label: 'Reasons for Fall in Profit',
                data: [40, 25, 15, 10, 10],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 206, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        };

        new Chart(ctxProfit, {
            type: 'doughnut',
            data: reasonsForFallInProfitData,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Fall in Profit Reasons'
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                return label + ': ' + value + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
