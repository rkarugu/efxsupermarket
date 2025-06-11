<div class="col-md-8 dashboard-card">
    {{-- <div class="date-filter">
        <div class="input-group">
            <label for="start-date">Start Date:</label>
            <input type="date" id="start-date" name="start-date">
        </div>
        <div class="input-group">
            <label for="end-date">End Date:</label>
            <input type="date" id="end-date" name="end-date">
        </div>
        <button id="apply-filter-btn"><i class="fas fa-sync-alt"></i></button>
    </div> --}}
    <canvas id="myChart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // sales, revenue and returns chart start
        const ctx = document.getElementById('myChart');

        const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
            'October', 'November', 'December'
        ];

        const dataset1 = {
            label: 'Sales',
            data: [65, 59, 80, 81, 56, 55, 40, 30, 20, 10, 5, 15],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            stack: 'combined',
            type: 'bar'
        };

        const dataset2 = {
            label: 'Revenue',
            data: [28, 48, 40, 19, 86, 27, 90, 50, 70, 45, 60, 35],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            stack: 'combined'
        };

        const dataset3 = {
            label: 'Returns',
            data: [10, 20, 15, 25, 30, 20, 15, 10, 5, 8, 12, 18],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderDash: [5, 5],
            borderWidth: 2,
            pointRadius: 0,
            type: 'line'
        };

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [dataset1, dataset2, dataset3]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Sales vs Revenue vs Returns'
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
