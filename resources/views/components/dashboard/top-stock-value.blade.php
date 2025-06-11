<div id="stockValue" style="height: 350px; width:100%; border: 1px solid #eee"
    class="d-flex align-items-center justify-content-center">
    <div class="loading">
        <h4><i class="fa fa-spinner fa-spin"></i> Loading...</h4>
    </div>
</div>
@push('scripts')
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.stock-value') }}",
                data: {
                    location: $("#store").val()
                },
                success: function(response) {
                    $("#stockValue .loading").hide();
                    renderStockValueChart(response.items, response.values)
                }
            })
        })

        function renderStockValueChart(items, values) {
            var options = {
                series: [{
                    data: values
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        borderRadiusApplication: 'end',
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: items                
                }
            };

            var chart = new ApexCharts(document.querySelector("#stockValue"), options);
            chart.render();
        }
    </script>
@endpush
