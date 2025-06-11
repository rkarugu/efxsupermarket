<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3 id="over_stocked_items"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Overstocked Items</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a id="over_stocked_items_link"
                href="{{ route('inventory-reports.overstock-report.index', ['from' => $from, 'to' => $to]) }}"
                class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3 id="reorder_items"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Reorder Items</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a id="reorder_items_link"
                href="{{ route('inventory-reports.reorder-items-report.index', ['from' => $from, 'to' => $to]) }}"
                class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3 id="missing_items"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Missing Items</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a id="missing_items_link"
                href="{{ route('inventory-reports.missing-items-report.index', ['from' => $from, 'to' => $to]) }}"
                class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="smaller-box">
                            <h3 id="slow_moving_items"><i class="fa fa-spinner fa-spin"></i></h3>
                            <p>Slow Moving</p>
                            <a id="slow_moving_items_link"
                                href="{{ route('inventory-reports.slow-moving-items-report.index', ['from' => $from, 'to' => $to, 'sold' => 5]) }}"
                                class="smaller-box-footer" target="_blank">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="smaller-box">
                            <h3 id="dead_stock_items"><i class="fa fa-spinner fa-spin"></i></h3>
                            <p>Dead Stock</p>
                            <a id="dead_stock_items_link"
                                href="{{ route('inventory-reports.dead-stock-report.index', ['from' => $from, 'to' => $to]) }}"
                                class="smaller-box-footer" target="_blank">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <style>
        .smaller-box {
            position: relative;
            display: block;
            text-align: center;
        }

        .smaller-box .smaller-box-footer {
            position: relative;
            text-align: center;
            padding: 3px 0;
            color: #fff;
            color: rgba(255, 255, 255, 0.8);
            display: block;
            z-index: 10;
            background: rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(function() {
            let store = $("#store").val();
            $("#over_stocked_items_link").prop('href', function(_, href) {
                return href + "&location=" + store;
            });
            $("#reorder_items_link").prop('href', function(_, href) {
                return href + "&location=" + store;
            });
            $("#missing_items_link").prop('href', function(_, href) {
                return href + "&location=" + store;
            });
            $("#slow_moving_items_link").prop('href', function(_, href) {
                return href + "&location=" + store;
            });
            $("#dead_stock_items_link").prop('href', function(_, href) {
                return href + "&location=" + store;
            });

            $.ajax({
                method: "get",
                url: "{{ route('procurement-dashboard.stock-stats', ['type' => 'over_stocked_items']) }}",
                data: {
                    store: store,
                },
                success: function(response) {
                    $("#over_stocked_items").text(response.count);
                }
            })

            $.ajax({
                method: "get",
                url: "{{ route('procurement-dashboard.stock-stats', ['type' => 'reorder_items']) }}",
                data: {
                    store: store,
                },
                success: function(response) {
                    $("#reorder_items").text(response.count);
                }
            })

            $.ajax({
                method: "get",
                url: "{{ route('procurement-dashboard.stock-stats', ['type' => 'missing_items']) }}",
                data: {
                    store: store,
                },
                success: function(response) {
                    $("#missing_items").text(response.count);
                    $("#missing_items a")
                }
            })

            $.ajax({
                method: "get",
                url: "{{ route('procurement-dashboard.stock-stats', ['type' => 'slow_moving_items']) }}",
                data: {
                    store: store,
                },
                success: function(response) {
                    $("#slow_moving_items").text(response.count);
                }
            })

            $.ajax({
                method: "get",
                url: "{{ route('procurement-dashboard.stock-stats', ['type' => 'dead_stock_items']) }}",
                data: {
                    store: store,
                },
                success: function(response) {
                    $("#dead_stock_items").text(response.count);
                }
            })
        })
    </script>
@endpush
