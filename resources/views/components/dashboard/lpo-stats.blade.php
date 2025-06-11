<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3 id="branch_requisitions"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Branch Requisitions</p>
            </div>
            <div class="icon">
                <i class="ion ion-document"></i>
            </div>
            <a href="{{ route('resolve-requisition-to-lpo.index') }}" class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3 id="pending_lpos"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Pending LPOs/Goods</p>
            </div>
            <div class="icon">
                <i class="ion ion-clock"></i>
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3 id="locked_suppliers"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Locked Suppliers</p>
            </div>
            <div class="icon">
                <i class="ion ion-lock-combination"></i>
            </div>
            <a href="{{ route('trade-agreement.index') }}" class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3 id="signed_portal"><i class="fa fa-spinner fa-spin"></i></h3>
                <p>Signed in Portal</p>
            </div>
            <div class="icon">
                <i class="ion ion-checkmark-circled"></i>
            </div>
            <a href="{{ route('trade-agreement.index') }}" class="small-box-footer" target="_blank">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.lpo-stats') }}",
                method: "GET",
                data: {
                    store: $("#store").val(),
                },
                success: function(response) {
                    $("#branch_requisitions").text(response.branch_requisitions);
                    $("#pending_lpos").text(response.pending_lpos);
                    $("#locked_suppliers").text(response.locked_suppliers+"/"+response.total_suppliers);
                    $("#signed_portal").text(response.signed_portal+"/"+response.total_suppliers);
                }
            })
        })
    </script>
@endpush
