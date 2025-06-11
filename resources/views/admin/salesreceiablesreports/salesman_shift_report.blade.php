@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">SalesMan shift Report</h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form method="get">
                    <div class="row">
                        <div class="form-group col-sm-3">

                            {!! Form::text('from', request()->from, [
                                'maxlength' => '255',
                                'placeholder' => 'Start Date',
                                'required' => true,
                                'class' => 'form-control datepicker',
                                'readonly' => true,
                            ]) !!}
                        </div>

                        <div class="form-group col-md-3">
                            {!! Form::text('to', request()->to, [
                                'maxlength' => '255',
                                'placeholder' => 'End Date',
                                'required' => true,
                                'class' => 'form-control datepicker',
                                'readonly' => true,
                            ]) !!}
                        </div>

                        <div class="form-group col-sm-3">
                            {!! Form::select('sales_man', $sales_people, request()->sales_man, [
                                'placeholder' => 'Select Route',
                                'class' => 'form-control mlselect',
                                'required' => true,
                            ]) !!}
                        </div>

                        <div class="form-group col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="submit" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf"></i></button>
                            <button title="Print" type="button" class="btn btn-danger" name="manage-request"
                                    onclick="printgrn(this)" value="print">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </button>

                        </div>
                    </div>
                </form>

                <hr>

                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Shift</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Invoices</th>
                        <th>Total Returns</th>
                        <th>Total Discounts</th>
                        <th>Receipts During Shift</th>
                        <th>Receipts After Shift</th>
                        <th>Total Receipts</th>
                        <th>Opening Balance</th>
                        <th>Closing Balance</th>
                    </tr>
                    </thead>
                    @if($show)
                        <tbody>
                        @foreach ($shiftsData as $index => $shift)
                            <tr class="parent-row">
                                <td class="toggle-icon"><i class="fa fa-plus"></i></td>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $shift['shift_id'] }}</td>
                                <td>{{ $shift['start_date'] }}</td>
                                <td>{{ $shift['end_date'] }}</td>
                                <td>{{ number_format($shift['invoices']) }}</td>
                                <td>{{ number_format($shift['returns']) }}</td>
                                <td>{{ number_format($shift['discounts']) }}</td>
                                <td>{{ number_format($shift['receipts_during_shift']) }}</td>
                                <td>{{ number_format($shift['receipts_outside_total']) }}</td>
                                <td>{{ number_format($shift['receipts']) }}</td>
                                <td>{{ number_format($shift['opening_balance']) }}</td>
                                <td>{{ number_format($shift['closing_balance']) }}</td>
                            </tr>
                            <tr id="child-row-{{ $index }}" class="collapse child-row">
                                <td colspan="10">
                                    <table class="table child-table">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Document No</th>
                                            <th>Customer Code</th>
                                            <th>Reference</th>
                                            <th>Receipt Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($shift['receipts_outside'] as $child)
                                            <tr>
                                                <td>{{ $child['trans_date'] }}</td>
                                                <td>{{ $child['customer_code'] }}</td>
                                                <td>{{ $child['document_no'] }}</td>
                                                <td>{{ $child['reference'] }}</td>
                                                <td>{{ number_format($child['amount']) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr class="totals">
                                            <td colspan="4">TOTAL</td>
                                            <td>{{ number_format($shift['receipts_outside_total'])}}</td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="totals">
                            <td colspan=5">Grand Totals</td>
                            <td>{{ number_format($grandTotalInvoices, 2) }}</td>
                            <td>{{ number_format($grandTotalReturns, 2) }}</td>
                            <td>{{ number_format($grandTotalDiscounts, 2) }}</td>
                            <td>{{ number_format($grandTotalReceiptsDuring,2) }}</td>
                            <td>{{ number_format($grandTotalReceiptsAfter, 2) }}</td>
                            <td>{{ number_format($grandTotalReceipts, 2) }}</td>
                            <td>{{ number_format($openingBalance, 2) }}</td>
                            <td>{{ number_format($openingBalance, 2) }}</td>
                        </tr>
{{--                        <tr class="totals">--}}
{{--                            <td colspan="5">Mapped Totals</td>--}}
{{--                            <td>{{ number_format($mappedInvoices), 2 }}</td>--}}
{{--                            <td>{{ number_format($mappedReturns), 2 }}</td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td>{{ number_format($mappedReceipts), 2 }}</td>--}}
{{--                        </tr>--}}
                        <tr class="totals">
                            <td colspan="7">Customer Balance</td>
                            <td>{{ number_format($grandTotalInvoices + $grandTotalReturns + $grandTotalReceipts), 2 }}</td>
                        </tr>
                        </tfoot>

                    @endif
                </table>

            </div>
        </div>
    </section>

@endsection


@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <style>
        #create_datatable1 .even {
            background-color: #ddd;
        }
    </style>
    <style>
        .totals {
            font-weight: bold;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script type="text/javascript">
        $(function() {

            $(".mlselect").select2();
        });

        function printgrn(input) {
            var postData = $(input).parents('form').serialize() + '&manage-request=print';
            var url = $(input).parents('form').attr('action');

            jQuery.ajax({
                url: url,
                type: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                data: postData,
                success: function(response) {

                    var divContents = response;
                    //alert(divContents);
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('.parent-row').click(function() {
                var icon = $(this).find('.toggle-icon i');
                var target = $(this).next('.child-row');

                if (target.hasClass('in')) {
                    icon.removeClass('fa-minus').addClass('fa-plus');
                } else {
                    icon.removeClass('fa-plus').addClass('fa-minus');
                }

                $('.child-row.in').not(target).collapse('hide');
                target.collapse('toggle');
            });
        });
    </script>


@endsection
