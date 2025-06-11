<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3 id="total_payable"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Total Payable</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="{{ route('maintain-suppliers.index') }}" class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3 id="pending_grns"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Pending GRNs</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{ route('pending-grns.index') }}" class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3 id="unpaid_invoices"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Unpaid Invoices</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('maintain-suppliers.processed_invoices.index') }}" class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3 id="pending_vouchers"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Processing Payment</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{ route('payment-vouchers.index') }}" class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>   
</div>
@push('scripts')
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.supplier-stats') }}",
                method: "GET",
                data: {
                    store: $("#store").val(),
                },
                success: function(response) {
                    $("#total_payable").text(response.total_payable);
                    $("#pending_grns").text(response.pending_grns);
                    $("#unpaid_invoices").text(response.unpaid_invoices);
                    $("#pending_vouchers").text(response.pending_vouchers);
                }
            })
        })
    </script>
@endpush
