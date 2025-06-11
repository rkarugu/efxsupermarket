<div>
    <div class="box-header with-border"><h3 class="box-title">  Payments by Channel</h3>
    </div>
    <div class="box-body">
        <div class="row pb-4">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    {!!Form::select('payment_method', $paymentMethods, null, ['placeholder'=>'Select Account ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Account','id'=>'payment_method'  ])!!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">From</label>
                    <input type="date" name="start_date" id="start_date_tender" class="form-control" value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">To</label>
                    <input type="date" name="end_date" id="end_date_tender" class="form-control"  value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <button type="submit" id="filterChannels" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                </div>
            </div>
        </div>
        <table class="table table-striped mt-3" id="channelsTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Date and Time</th>
                <th>Cashier</th>
                <th>Payment Method</th>
                <th>Branch</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th colspan="5" style="text-align:right">Total:</th>
                <td></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        var VForm = new Form();
        $(document).ready(function() {
            /*table for channel trans*/
            var table = $("#channelsTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{!! route('cashier-management.tender-transactions', base64_encode($cashier->id)) !!}',
                    data: function(data) {
                        var from = $('#start_date_tender').val();
                        var to = $('#end_date_tender').val();
                        data.payment_method = $('#payment_method').val();
                        data.from = from;
                        data.to = to;
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "parent.user.name",
                        name: "cashier"
                    },
                    {
                        data: "method.title",
                        name: "method.title"
                    },
                    {
                        data: "parent.branch.name",
                        name: "parent.branch.name"
                    },
                    {
                        data: "amount",
                        name: "amount",
                        searchable: false
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    let api = this.api();

                    // Remove the formatting to get integer data for summation
                    let intVal = function (i) {
                        return typeof i === 'string'
                            ? i.replace(/[\$,]/g, '') * 1
                            : typeof i === 'number'
                                ? i
                                : 0;
                    };

                    // Total over all pages
                    total = api
                        .column(5)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Update footer
                    api.column(5).footer().innerHTML =
                        total.toLocaleString('en-US', {
                            style: 'decimal',
                            minimumFractionDigits: 2
                        });

                }
            });
            $('#filterChannels').click(function(e){
                e.preventDefault();
                table.draw();
            });
        })
    </script>
@endpush