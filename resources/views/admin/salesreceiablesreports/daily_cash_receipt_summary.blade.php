@extends('layouts.admin.admin')

@section('content')
    <style>
        .buttons-html5 {
            background-color: #f39c12 !important;
            border-color: #e08e0b !important;
            border-radius: 3px !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: 1px solid transparent !important;
            color: #fff !important;
            display: inline-block !important;
            padding: 7px 10px !important;
            margin-bottom: 0 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            line-height: 1.42857143 !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            width: 4% !important;
            position: relative !important;
            left: 162px !important;
            margin-top: -129px;
        }
    </style>


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Daily Cash Receipt Summary Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    <div class="card-header">
                        <i class="fa fa-filter"></i> Filter
                    </div><br>
                    {!! Form::open(['route' => 'sales-and-receivables-reports.daily-cash-receipt-summary', 'method' => 'get']) !!}

                    <div>
                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::text('start-date', null, [
                                        'class' => 'datepicker form-control',
                                        'placeholder' => 'Start Date',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::text('end-date', null, [
                                        'class' => 'datepicker form-control',
                                        'placeholder' => 'End Date',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 no-padding-h">



                            <div class="col-sm-1">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success" name="manage-request"
                                        value="filter">Filter</button>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request"
                                    value="xls"><i class="fa fa-file-excel" aria-hidden="true"></i>
                                </button>
                            </div>


                        </div>


                    </div>

                    </form>
                </div>

                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable2">
                        <thead>
                            <tr>
                                <th width="10%">S.No.</th>
                                <th width="10%">Receipt No</th>
                                <th width="15%">Date</th>
                                <th width="20%">Customer Name</th>
                                <th width="10%">Payment Method</th>
                                <th width="15%">Cashier Name</th>
                                <th width="10%">Reference</th>

                                <th width="10%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            $final_amount = [];
                            
                            ?>

                            @foreach ($all_item as $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $item->document_no }}</td>
                                    <td>{{ date('Y-m-d', strtotime($item->trans_date)) }}</td>


                                    <td>{{ getCustomerNameByDocumentNumber($item->document_no) }}</td>



                                    <td>{{ $item->getPaymentMethod->title }}</td>

                                    <td>{{ $item->getCashierDetail ? $item->getCashierDetail->name : '' }}</td>
                                    <td>{{ $item->reference }}</td>
                                    <td>{{ manageAmountFormat($item->amount) }}</td>




                                </tr>
                                <?php
                                
                                $final_amount[] = $item->amount;
                                $i++; ?>
                            @endforeach



                        </tbody>

                        <tfoot style="font-weight: bold;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td></td>
                            <td>{{ manageAmountFormat(array_sum($final_amount)) }}</td>

                        </tfoot>
                    </table>
                </div>


            </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>

    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" class="init">
        $(document).ready(function() {
            $('#create_datatable2').DataTable({
                pageLength: "100",
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf" aria-hidden="true">',
                    exportOptions: {
                        modifier: {
                            page: 'current'
                        }
                    },
                    customize: function(doc) {
                        doc.content[1].table.widths = ["*", "*", "*", "*", "*", "*", "*", "*"];
                    }

                }]
            });
        });



        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
