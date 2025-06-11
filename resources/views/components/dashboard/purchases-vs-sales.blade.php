<div id="purchaseAgainstSales" style="height: 350px; width:100%; border: 1px solid #eee" class="d-flex align-items-center justify-content-center">
    <div class="loading">
        <h4><i class="fa fa-spinner fa-spin"></i> Loading...</h4>
    </div>
</div>
@push('scripts')
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.purchases-vs-sales') }}",
                data: {
                    location: $("#store").val()
                },
                success: function(response) {
                    $("#purchaseAgainstSales .loading").hide();
                    renderChart(response.purchases, response.sales)
                }
            })
        })

        function renderChart(purchases, sales) {
            var options = {
                series: [{
                    name: 'Purchases',
                    data: purchases
                }, {
                    name: 'Sales',
                    data: sales
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
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
                    categories: {!! $months !!},
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return value.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                },
                fill: {
                    opacity: 1
                }
            };

            var chart = new ApexCharts(document.querySelector("#purchaseAgainstSales"), options);
            chart.render();
        }
    </script>
@endpush
