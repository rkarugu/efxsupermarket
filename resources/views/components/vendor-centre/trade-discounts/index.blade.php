<div style="padding: 10px;">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#discountsTab" data-toggle="tab">Discounts</a></li>
        <li><a href="#discountDemandsTab" data-toggle="tab">Demands</a></li>
    </ul>
    <div class="tab-content" style="padding: 10px">
        <div class="tab-pane active" id="discountsTab">
            <form id="createDiscountForm" action="{{ route('trade-discounts.store') }}" method="post">
                @csrf
                <input type="hidden" name="supplier" value="{{ $supplier->id }}">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="discountMonth">Month</label>
                        <select name="month" id="discountMonth" class="form-control mselect">
                            <option value="">All</option>
                            @foreach ($months as $month)
                                <option value="{{ $month }}" @selected($month == date('F'))>
                                    {{ $month }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label for="discountYear">Year</label>
                        <select name="year" id="discountYear" class="form-control mselect">
                            @foreach ($years as $year)
                                <option value="{{ $year }}" @selected($year == date('Y'))>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label style="display:block">&nbsp;</label>
                        <button type="submit" id="createDiscountBtn" class="btn btn-primary">
                            <i class="fa fa-plus-circle"></i> Generate Discount
                        </button>
                        <button type="button" id="exportDiscountPdf" class="btn btn-primary">
                            <i class="fa fa-file-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
            </form>
            <div style="margin-top: 15px">
                <table class="table" id="tradeDiscountsDataTable">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Discount Type</th>
                            <th>Invoice No.</th>
                            <th>Invoice Date</th>
                            <th>Demand No.</th>
                            <th>Description</th>
                            <th>Prepared By</th>
                            <th>Approval</th>
                            <th>Invoice Amount</th>
                            <th>Disc. Amount</th>
                            <th>Approved Amount</th>
                            <th style="width:80px">Actions</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="text-right" colspan="9">Total</th>
                            <th class="text-right" id="totalAmount"></th>
                            <th class="text-right" id="totalApprovedAmount"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="tab-pane" id="discountDemandsTab">
            <form id="createDemandForm" action="{{ route('trade-discount-demands.store') }}" method="post">
                @csrf
                <input type="hidden" name="supplier" value="{{ $supplier->id }}">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="demandMonth">Month</label>
                        <select name="month" id="demandMonth" class="form-control mselect">
                            <option value="">All</option>
                            @foreach ($months as $month)
                                <option value="{{ $month }}" @selected($month == date('F'))>
                                    {{ $month }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label for="demandYear">Year</label>
                        <select name="year" id="demandYear" class="form-control mselect">
                            @foreach ($years as $year)
                                <option value="{{ $year }}" @selected($year == date('Y'))>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label style="display:block">&nbsp;</label>
                        <button type="submit" id="createDemandBtn" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> Create Demand
                        </button>
                    </div>
                </div>
            </form>
            <div style="margin-top: 15px">
                <table class="table" id="tradeDiscountDemandsDataTable">
                    <thead>
                        <tr>
                            <th>Demand No</th>
                            <th>Reference</th>
                            <th>CU Invoice No.</th>
                            <th>Note Date</th>
                            <th>Memo</th>
                            <th>Prepared By</th>
                            <th>Processed</th>
                            <th>Processed By</th>
                            <th>Date Processed</th>
                            <th>Credit Note No.</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="text-right" colspan="10">Total</th>
                            <th class="text-right" id="totalDemandAmount"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" role="dialog" id="approveDiscountModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Approve Discount</h4>
            </div>
            <form id="updateDiscountForm" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="discount" id="discountId">
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>Invoice No.</th>
                            <td><span id="discountInvoice"></span></td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><span id="discountAmount"></span></td>
                        </tr>
                        <tr>
                            <th>Approve</th>
                            <td>
                                <input type="checkbox" name="approve" id="discountApproved">
                            </td>
                        </tr>
                        <tr>
                            <th>Approved Amount</th>
                            <td>
                                <input type="number" name="approved_amount" id="discountApprovedamount"
                                    class="form-control" min="0.01">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        &times; Close</button>
                    <button type="submit" class="btn btn-primary" id="updateDiscountBtn">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#discountMonth, #discountYear").change(function() {
                refreshTable($("#tradeDiscountsDataTable"));
            });

            let discounts = $("#tradeDiscountsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: "{{ route('trade-discounts.index') }}",
                    data: function(data) {
                        data.supplier = "{{ $supplier->id }}"
                        data.year = $("#discountYear").val()
                        data.month = $("#discountMonth").val()
                    }
                },
                columns: [{
                    data: "id",
                    name: "id",
                }, {
                    data: "discount_type",
                    name: "agreements.discount_type",
                }, {
                    data: "supplier_invoice_number",
                    name: "supplier_invoice_number",
                }, {
                    data: "invoice_date",
                    name: "invoice_date",
                }, {
                    data: "demand_no",
                    name: "demand_no",
                }, {
                    data: "description",
                    name: "description",
                }, {
                    data: "prepared_by.name",
                    name: "preparedBy.name",
                }, {
                    data: "status",
                    name: "status",
                }, {
                    data: "invoice_amount",
                    name: "trade_discounts.invoice_amount",
                    className: "text-right",
                }, {
                    data: "amount",
                    name: "trade_discounts.amount",
                    className: "text-right",
                }, {
                    data: "approved_amount",
                    name: "approved_amount",
                    className: "text-right",
                }, {
                    data: "actions",
                    name: "actions",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                }, ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalAmount").html(json.total_amount);
                    $("#totalApprovedAmount").html(json.total_approved_amount);
                }
            })

            discounts.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $("#demandMonth, #demandYear").change(function() {
                refreshTable($("#tradeDiscountDemandsDataTable"));
            });

            let demands = $("#tradeDiscountDemandsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: "{{ route('trade-discount-demands.index') }}",
                    data: function(data) {
                        data.supplier = "{{ $supplier->id }}"
                        data.year = $("#demandYear").val()
                        data.month = $("#demandMonth").val()
                    }
                },
                columns: [{
                    data: "demand_no",
                    name: "demand_no",
                }, {
                    data: "supplier_reference",
                    name: "supplier_reference",
                }, {
                    data: "cu_invoice_number",
                    name: "cu_invoice_number",
                }, {
                    data: "note_date",
                    name: "note_date",
                }, {
                    data: "memo",
                    name: "memo",
                }, {
                    data: "prepared_by",
                    name: "initiators.name",
                }, {
                    data: "status",
                    name: "status",
                }, {
                    data: "processed_by",
                    name: "processors.name",
                }, {
                    data: "processed_at",
                    name: "processed_at",
                }, {
                    data: "credit_note_no",
                    name: "credit_note_no",
                }, {
                    data: "amount",
                    name: "trade_discounts.amount",
                    className: "text-right",
                }, {
                    data: "actions",
                    name: "actions",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                }, ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalDemandAmount").html(json.total_amount);
                }
            })

            demands.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $("#tradeDiscountsDataTable tbody").on('click', '.edit-discount', function(e) {
                e.preventDefault();
                let discount = $(this).data('discount');
                let action = $(this).data('discount-action');

                $("#discountInvoice").text(discount.supplier_invoice_number)
                $("#discountAmount").text(Number(discount.amount).formatMoney())
                $("#discountApprovedamount").val(discount.approved_amount)

                if (discount.status) {
                    $("#discountApproved").prop('checked', true);
                } else {
                    $("#discountApproved").prop('checked', false);
                }
                $("#updateDiscountForm").prop('action', action)

                $("#approveDiscountModal").modal('show');
            })

            $('#tradeDiscountsDataTable tbody').on('click', '[data-toggle="discounts"]', function(e) {
                e.preventDefault();

                let action = $(this).data('action');
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure want to delete discount?',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes, I Confirm',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = $(target).attr('action');
                        $.ajax({
                            url: url,
                            data: $(target).serialize(),
                            method: 'POST',
                            success: function(response) {
                                if (!response.success) {
                                    return form.errorMessage(response.message);
                                }

                                form.successMessage(response.message);
                                refreshTable($("#tradeDiscountsDataTable"));
                            },
                            error: function(err) {
                                form.errorMessage('Something went wrong');
                            }
                        });
                    }
                })
            });

            $('#tradeDiscountDemandsDataTable tbody').on('click', '[data-toggle="demands"]', function(e) {
                e.preventDefault();

                let action = $(this).data('action');
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure want to delete demand?',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes, I Confirm',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = $(target).attr('action');
                        $.ajax({
                            url: url,
                            data: $(target).serialize(),
                            method: 'POST',
                            success: function(response) {
                                if (!response.success) {
                                    return form.errorMessage(response.message);
                                }

                                form.successMessage(response.message);
                                reloadtables();
                            },
                            error: function(err) {
                                form.errorMessage('Something went wrong');
                            }
                        });
                    }
                })
            });

            var form = new Form();
            $("#createDiscountForm").on("submit", function(e) {
                e.preventDefault();
                let btn = $("#createDiscountBtn")
                let btnHtml = btn.html();
                var url = $(this).attr('action');

                btn.text('Loading...').prop('disabled', true)

                $.ajax({
                    url: url,
                    data: $(this).serialize(),
                    method: 'POST',
                    success: function(response) {
                        btn.html(btnHtml).prop('disabled', false)
                        if (!response.success) {
                            return form.errorMessage(response.message);
                        }

                        btn.html(btnHtml).prop('disabled', false)
                        form.successMessage(response.message);
                        reloadtables();
                    },
                    error: function(err) {
                        btn.html(btnHtml).prop('disabled', false)
                        form.errorMessage('Something went wrong!');
                    }
                });
            })

            $("#updateDiscountForm").on("submit", function(e) {
                e.preventDefault();

                let btnHtml = $("#updateDiscountBtn").html();
                var url = $(this).attr('action');

                $("#updateDiscountBtn").text('Loading...')

                $.ajax({
                    url: url,
                    data: $(this).serialize(),
                    method: 'POST',
                    success: function(response) {
                        $("#approveDiscountModal").modal('hide');
                        $("#updateDiscountBtn").html(btnHtml)
                        form.successMessage(response.message);
                        reloadtables()
                    },
                    error: function(err) {
                        $("#approveDiscountModal").modal('hide');
                        $("#updateDiscountBtn").html(btnHtml)
                        form.errorMessage('Something went wrong');
                    }
                });
            });

            $("#exportDiscountPdf").on('click', function() {
                let url = "{{ route('trade-discounts.index', ['supplier' => $supplier->id]) }}";
                let month = $("#discountMonth").val();
                let year = $("#discountYear").val();

                window.location.replace(url + '&month=' + month + '&year=' + year+'&download=pdf');
            });

            $("#createDemandForm").on("submit", function(e) {
                e.preventDefault();
                let btn = $("#createDemandBtn")
                let btnHtml = btn.html();
                var url = $(this).attr('action');

                btn.text('Loading...').prop('disabled', true)

                $.ajax({
                    url: url,
                    data: $(this).serialize(),
                    method: 'POST',
                    success: function(response) {
                        btn.html(btnHtml).prop('disabled', false)
                        if (!response.success) {
                            return form.errorMessage(response.message);
                        }

                        btn.html(btnHtml).prop('disabled', false)
                        form.successMessage(response.message);
                        reloadtables();
                    },
                    error: function(err) {
                        btn.html(btnHtml).prop('disabled', false)
                        form.errorMessage('Something went wrong!');
                    }
                });
            })

            function reloadtables() {
                refreshTable($("#tradeDiscountsDataTable"));
                refreshTable($("#tradeDiscountDemandsDataTable"));
            }
        });
    </script>
@endpush
