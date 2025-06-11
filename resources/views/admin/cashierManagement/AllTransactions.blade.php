@extends('layouts.admin.admin')
@section('content')

    <a href="{{ route($pmodule.'.index') }}" class="btn btn-primary">Back</a>
    <br>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} : All Transactions</h3>
            </div>
            <div class="box-body">
                <div class="row pb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
{{--                    <div class="col-md-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="">To</label>--}}
{{--                            <input type="date" name="end_date" id="end_date" class="form-control"  value="{{request()->input('end-date') ?? date('Y-m-d')}}">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    @if($permission == 'superadmin')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="restaurant_id">Branch</label>
                                {!!Form::select('restaurant_id', $branches, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Branch','id'=>'restaurant_id'  ])!!}
                            </div>
                        </div>
                    @endif


{{--                    <div class="col-md-3">--}}
{{--                        <select name="store" id="inputstore" class="form-control mlselec6t">--}}
{{--                            <option value="" selected disabled> Select Store Location</option>--}}
{{--                            @foreach(getStoreLocationDropdown() as $index => $store)--}}
{{--                                <option value="{{$index}}" {{request()->store == $index ? 'selected' : ''}}>{{$store}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                        </div>
                    </div>
                </div>
                <table class="table table-striped" id="cashiersTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Cashier</th>
                        <th>Received By</th>
                        <th>Branch</th>
                        <th>Date Time</th>
                        <th>Amount</th>
                        <th>Bank Reference</th>
                        <th>Banked Amount</th>
                        <th>Cash in Hand</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th colspan="6" style="text-align:right">Total:</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>

                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="yourModalId" tabindex="-1" aria-labelledby="yourModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="yourModalLabel">Drop Cash</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>


@endsection
@push('scripts')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        var VForm = new Form();
        $(document).ready(function() {
            var table = $("#cashiersTable").DataTable({
                "lengthMenu": [10, 25, 50, 100],
                "pageLength": 100,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{!! route('cashier-management.transactions') !!}',
                    data: function(data) {
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        var restaurant_id = $('#restaurant_id').val();
                        data.restaurant_id = restaurant_id;
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
                        data: "reference",
                        name: "reference"
                    },
                    {
                        data: "cashier.name",
                        name: "cashier.name",
                    },
                    {
                        data: "user.name",
                        name: "receiver",
                        searchable: false
                    },
                    {
                        data: "cashier.branch.name",
                        name: "cashier",
                        searchable: false
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "amount",
                        name: "amount"
                    },
                    {
                        data: "bank_receipt_number",
                        name: "bank_receipt_number"
                    },
                    {
                        data: "banked",
                        name: "banked",
                        searchable: false
                    },
                    {
                        data: "unbanked",
                        name: "unbanked",
                        searchable: false
                    }
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
                        .column(6)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    banked = api
                        .column(8)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);


                    balance = api
                        .column(9)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);


                    // Update footer
                    api.column(6).footer().innerHTML =
                        total.toLocaleString('en-US', {
                            style: 'decimal',
                            minimumFractionDigits: 2
                        });

                    api.column(8).footer().innerHTML =
                        banked.toLocaleString('en-US', {
                            style: 'decimal',
                            minimumFractionDigits: 2
                        });
                    api.column(9).footer().innerHTML =
                        balance.toLocaleString('en-US', {
                            style: 'decimal',
                            minimumFractionDigits: 2
                        });
                }
            });
            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
            });
            $('#dropCashForm').submit(function(event) {
                var amount = parseFloat($('#amount').val());
                var max = parseFloat($('#cash_at_hand').val());

                // Check if amount is not empty and less than or equal to 3000
                if (isNaN(amount) || amount <= 1 || amount > max) {
                    $('.error-message').text('Amount must be between 1 and '+max).addClass('text-danger');
                    event.preventDefault();
                } else {
                    $('.error-message').text('').removeClass('text-danger');
                    event.preventDefault()
                    saveFormDetails();
                }
            });
            function saveFormDetails() {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var form = $('#dropCashForm');
                var formData = form.serialize();
                formData += '&_token=' + encodeURIComponent(csrfToken);

                $.ajax({
                    url: '{{ route('cashier-management.drop') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        VForm.successMessage('Cash Dropped Successfully');
                        table.ajax.reload();
                        $('#amount').val(0);
                    },

                    error: function(error) {
                        var errorMessage = error.responseJSON.message;
                        $('#error-message-container').show()
                        $('#error-message-container').html(errorMessage); // Replace with your error message container ID
                    }
                });
            }
            $(".mlselec6t").select2();
        })

    </script>
@endpush