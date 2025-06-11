<div class="card-content">
    <canvas id="reasonsForReturnsChart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Doughnut chart for reasons for returns
        const ctxReturns = document.getElementById('reasonsForReturnsChart').getContext('2d');

        const reasonsForReturnsData = {
            labels: ['Damaged', 'Wrong Item', 'Late Delivery', 'Changed Mind', 'Other'],
            datasets: [{
                label: 'Reasons for Returns',
                data: [30, 25, 20, 15, 10],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 206, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        };

        new Chart(ctxReturns, {
            type: 'doughnut',
            data: reasonsForReturnsData,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Reasons for Returns'
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
