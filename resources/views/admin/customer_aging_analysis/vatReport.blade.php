@extends('layouts.admin.admin')

@section('content')
    <style>
        .buttons-excel {
            background-color: #f39c12 !important;
            border-color: #e08e0b !important;
            border-radius: 3px !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: 1px solid transparent !important;
            color: #fff !important;
            display: inline-block !important;
            padding: 6px 12px !important;
            margin-bottom: 0 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            line-height: 1.42857143 !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        .dt-buttons {
            width: 10% !important;
            position: relative !important;
            left: 74px !important;
            top: -91px !important;
        }
    </style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">

                <div class="box-header with-border">
                  <div class="d-flex justify-content-between">
                      <h3 class="box-title">Vat Report</h3>
                      <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                          << Back to Sales and Receivables Reports </a>
                  </div>
              </div>

                <div style="height: 150px ! important;">

                    <br>

                    <form action="" method="get">
                        <div>
                            <div class="col-md-12 no-padding-h">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                            value="{{ $start_date }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                            value="{{ $end_date }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <select name="pin" class="form-control">
                                            <option value="" selected>Select Customer Pin </option>
                                            <option value="NotNull" @if (isset($pin) && $pin == 'NotNull') selected @endif> With
                                                Pin</option>
                                            <option value="" @if (isset($pin) && $pin == '') selected @endif>Without
                                                Pin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <select name="tax_manager_id" class="form-control">
                                            <option value="" selected>Select Vat Category</option>
                                            <option value="1" @if (isset($tax) && $tax == '1') selected @endif> Vat
                                                16% Report</option>
                                            <option value="2" @if (isset($tax) && $tax == '2') selected @endif>Zero
                                                Vat Report</option>
                                            <option value="3" @if (isset($tax) && $tax == '3') selected @endif> Vat
                                                Exempted Report</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 no-padding-h">
                                <div class="col-sm-1" style="margin-top: -20px"><button type="submit"
                                        class="btn btn-success" name="manage-request" value="filter">Filter</button></div>
                                <div class="col-sm-1">
                                    <!--  <button title="Export In Excel" type="submit" class=" btn btn-warning" name="manage-request" value="xls"  ><i class="fa fa-file-excel" aria-hidden="true"></i>
                                    </button> -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div> @include('message') <div class="col-md-12 no-padding-h">

                    <table class="table table-bordered table-hover" id="create_datatable2" style="margin-top: -30px;">

                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th>Vat Group</th>
                                <th>Customer Pin</th>
                                <th>Customer Name</th>
                                <th>Date</th>
                                <th>Cu Invoice Number</th>
                                <th>Description</th>
                                <th>Tax Amount</th>
                                <th>Amount Exclusive of VAT</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                use Carbon\Carbon;
                                $totalVat = 0;
                                $totalAmount = 0;
                            @endphp
                            @foreach ($customer as $val)
                                @php
                                    $rate = $val['vat_rate'] == 0 ? 0 : $val['vat_rate'] / 100;
                                    if ($rate > 0) {
                                        $vat = $val['selling_price'] * $val['quantity'] * $rate;
                                    } else {
                                        $vat = 0;
                                    }
                                @endphp
                                <tr>
                                    <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                    <td>
                                        @if ($val['tax_manager_id'] == 1)
                                            VAT 16%
                                        @elseif($val['tax_manager_id'] == 2)
                                            Zero Vat
                                        @elseif($val['tax_manager_id'] == 3)
                                            Vat Exempted
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $val['customer_pin'] }}</td>
                                    <td>{{ $val['name'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($val['requisition_date'])->format('d/m/Y') }}</td>
                                    <td>{{ $val['cu_invoice_number'] }}</td>
                                    <td>ETIMS/TIMS sales</td>
                                    <td>{{ number_format($vat, 2) }}</td>
                                    <th>{{ number_format(floatval($val['selling_price']) * floatval($val['quantity']), 2) }}
                                    </th>
                                </tr>
                                @php
                                    $totalVat += $vat;
                                    $totalAmount += floatval($val['selling_price']) * floatval($val['quantity']);
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7">Total:</th>
                                <th>{{ number_format($totalVat, 2) }}</th>
                                <th>{{ number_format($totalAmount, 2) }}</th>
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
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
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
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" class="init">
        $(document).ready(function() {
            $('#create_datatable2').DataTable({
                "lengthMenu": [
                    [25, 100],
                    [25, 100, "All"]
                ],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel" aria-hidden="true">',
                    footer: true,
                    exportOptions: {
                        modifier: {
                            search: 'applied',
                            order: 'applied'
                        }
                    }
                }, ]
            });
        });
    </script>
@endsection
