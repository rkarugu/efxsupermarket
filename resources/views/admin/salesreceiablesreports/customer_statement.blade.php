    @extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Customer Statement Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
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
                            {!! Form::select('customer_id', $customerAccounts, request()->customer_id, [
                                'placeholder' => 'Select Customer Name',
                                'class' => 'form-control mlselect',
                                'required' => true,
                            ]) !!}
                        </div>

                        <div class="form-group col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button title="Print" type="button" class="btn btn-danger" name="manage-request"
                                onclick="printgrn(this)" value="print">

                                {{-- <i class="fa fa-print" aria-hidden="true"></i> --}}
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>

                            </button>

                            {{-- <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request"
                                value="pdf">
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </button> --}}
                            <button title="Export In Excel" type="submit" class="btn btn-success" name="manage-request"
                                value="excel">
                                <i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-invert" id="create_datatable1">
                        <thead>
                            <tr>
                                <td style="text-align: right;" colspan="8">
                                    <b>Opening Balance : {{ manageAmountFormat($getOpeningBlance) }}</b><br>
                                </td>
                            </tr>
                            <tr>
                                <th style="text-align: left; width:11%">Date
                                </th>
                                <th style="text-align: left; width:7%">Type
                                </th>
                                <th style="text-align: left; width:10%">Document
                                </th>
                                <th style="text-align: left; width:31%">Name/Reference
                                </th>
                                <th style="text-align: right; width:11%">Debit
                                </th>
                                <th style="text-align: right; width:11%">Credit
                                </th>
                                <th style="text-align: right; width:10%">Trans Bal
                                </th>
                                <th style="text-align: right; width:13%">Balance
                                </th>

                            </tr>
                        </thead>
                        <?php
                        $total_amount = [];
                        $nvtotal_amount = [];
                        $pvtotal_amount = [];
                        $opBal = 0;
                        $data = [];
                        ?>
                        <tbody>
                            @foreach ($lists as $list)
                                @php
                                    $balance = $opBal + (float) $list->amount;
                                    $opBal = $balance;
                                @endphp

                                <tr>
                                    <td style="text-align: left;">
                                        {{ \Carbon\Carbon::parse($list->created_at)->format('Y-m-d H:i:s') }}</td>
                                    @php
                                        $type = isset($number_series_list[$list->type_number])
                                            ? $number_series_list[$list->type_number]
                                            : '';
                                        $doc_type = explode('-', $list->document_no);
                                    @endphp
                                    <td style="text-align: left;">{!! $type !!}</td>
                                    <td style="text-align: left;">
                                        @if (count($doc_type) > 0 && in_array($doc_type[0], ['RTN']))
                                            <a target="blank"
                                                href="{{ route('transfers.printReturnToPdf', ['transfer_no' => $list->document_no]) }}">{{ $list->document_no ? $list->document_no : '-' }}</a>
                                        @elseif(count($doc_type) > 0 && in_array($doc_type[0], ['INV']))
                                            <a target="blank"
                                                href="{{ route('transfers.printToPdf', ['transfer_no' => $list->document_no]) }}">{{ $list->document_no ? $list->document_no : '-' }}</a>
                                        @else
                                            {{ $list->document_no ?? '-' }}
                                        @endif
                                    </td>
                                    <td style="text-align: left;">{{ $list->reference }}</td>
                                    <td style="text-align: right;">
                                        {{ $list->amount > 0 ? manageAmountFormat($list->amount) : '' }}</td>
                                    <td style="text-align: right;">
                                        {{ $list->amount < 0 ? manageAmountFormat($list->amount) : '' }}</td>
                                    <td style="text-align: right;">{{ manageAmountFormat($list->amount) }}</td>

                                    <td style="text-align: right;">{{ manageAmountFormat($balance) }}</td>
                                </tr>
                                <?php $total_amount[] = $list->amount;
                                $nvtotal_amount[] = $list->amount < 0 ? $list->amount : 0;
                                $pvtotal_amount[] = $list->amount > 0 ? $list->amount : 0;
                                ?>
                            @endforeach

                        </tbody>

                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                    <b>B/F : {{ manageAmountFormat($getOpeningBlance) }}</b></td>

                                <td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                    <b>{{ manageAmountFormat(array_sum($pvtotal_amount)) }}</b></td>
                                <td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                    <b>{{ manageAmountFormat(array_sum($nvtotal_amount)) }}</b></td>
                                <th style="text-align: right;">Closing Bal</th>
                                <td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                    <b>{{ manageAmountFormat($closingBalance) }}</b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        #create_datatable1 .even {
            background-color: #ddd;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $(".mlselect").select2();
        });
    </script>

    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <?php
    $tableheading = '';
    
    if (isset($_GET['supplier_code']) && $_GET['supplier_code'] != '') {
        $tableheading = ' Supplier Name : ' . $supplierList[$_GET['supplier_code']];
    }
    if (isset($_GET['to']) && $_GET['to'] != '') {
        $tableheading .= '\n \n To : ' . $_GET['to'];
    }
    if (isset($_GET['from']) && $_GET['from'] != '') {
        $tableheading .= '\n \n From : ' . $_GET['from'];
    }
    ?>
    <script type="text/javascript" class="init">
        $(document).ready(function() {
            $('#create_datatable1').DataTable({
                pageLength: "100",
            });
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
@endsection
