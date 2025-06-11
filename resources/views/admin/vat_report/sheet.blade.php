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
            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    {{-- <div class="card-header">
                        <i class="fa fa-filter"></i> Filter
                    </div> --}}
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="box-title">Vat Report</h3>
                            {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                                << Back to Account Payables Rerports </a> --}}
                        </div>
                        <i class="fa fa-filter"></i> Filter
                    </div>
                    <br> {!! Form::open(['route' => 'vat-report.index', 'method' => 'get']) !!} <div>
                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">

                                <div class="form-group">
                                    <input type="date" name="start_date" value="{{ $start_date }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="date" name="end_date" value="{{ $end_date }}" class="form-control">
                                </div>

                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <select name="tax_manager_id" class="form-control">
                                        <option value="" selected>Select Vat Category</option>
                                        <option value="1" @if (isset($_GET['tax_manager_id']) && $_GET['tax_manager_id'] == '1') selected @endif> Vat 16%
                                            Report</option>
                                        <option value="2" @if (isset($_GET['tax_manager_id']) && $_GET['tax_manager_id'] == '2') selected @endif>Zero Vat
                                            Report</option>
                                        <option value="3" @if (isset($_GET['tax_manager_id']) && $_GET['tax_manager_id'] == '3') selected @endif> Vat
                                            Exempted Report</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-1">
                                <button type="submit" class="btn btn-success" name="manage-request"
                                    value="filter">Filter</button>
                            </div>
                            <!--  <div class="col-sm-1"><button type="submit" name="action" value="pdfvat" class="btn btn-primary">
                                            Print PDF
                                        </button></div>
                                         -->
                        </div>
                    </div>
                    </form>
                </div> @include('message') <div class="col-md-12 no-padding-h">
                    <h4>Vat Reports</h4>
                    <table class="table table-bordered table-hover" id="create_datatable2">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Supplier KRA Pin</th>
                                <th>Supplier Name</th>
                                <th>Item Description</th>
                                <th>Cu Invoice no</th>
                                <th>Create Date</th>
                                <th>Tax Group</th><!--
                      <th>Vat Amount</th> -->
                                <th>Total Amount Inc. VAT</th>
                                <th>Cu Invoice no (CR)</th>
                                <th>Date (CR)</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                                use Carbon\Carbon;
                            @endphp
                            @foreach ($customer as $val)
                                @php

                                    $invoiceInfo = json_decode($val['invoice_info']);
                                    $rate = $invoiceInfo->vat_rate == 0 ? 0 : $invoiceInfo->vat_rate / 100;
                                    if ($rate > 0) {
                                        $vat = $invoiceInfo->order_price * $val['qty_received'] * $rate;
                                    } else {
                                        $vat = 0;
                                    }

                                    $nonvat = floatval($val['FnAm']) - floatval($val['FnTaAm']);
                                    $nonvatInvoice =
                                        floatval($val['total_amount_inc_vat']) - floatval($val['vat_amount']);

                                @endphp

                                <tr>
                                    <td>Local</td>
                                    <td>{{ $val['kra_pin'] }}</td>
                                    <td>{{ $val['name'] }}</td>
                                    <td>ETIMS/TIMS PURCHASES</td>
                                    <td>
                                        @if (isset($val['FnCu']) && !empty($val['FnCu']))
                                            {{ $val['FnCu'] }}
                                        @elseif(isset($val['TCU']))
                                            {{ $val['TCU'] }}
                                        @endif
                                    </td>
                                    <td>

                                        @if (isset($val['FnCu']) && !empty($val['FnCu']))
                                            {{ \Carbon\Carbon::parse($val['note_date'])->format('d/m/Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($val['TRD'])->format('d/m/Y') }}
                                        @endif

                                    </td>
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
                                    <!-- <td> {{ number_format($vat, 2) }} -->
                                    </td>
                                    <td>

                                        @if (isset($val['FnCu']) && !empty($val['FnCu']))
                                            {{ number_format(-$nonvat, 2) }}
                                        @else
                                            {{ number_format($nonvatInvoice, 2) }}
                                        @endif
                                    </td>


                                    <td>
                                        @if (isset($val['FnCu']) && !empty($val['FnCu']))
                                            {{ $val['TCU'] }}
                                        @else
                                        @endif
                                    </td>



                                    <td>
                                        @if (isset($val['FnCu']) && !empty($val['FnCu']))
                                            {{ \Carbon\Carbon::parse($val['note_date'])->format('d/m/Y') }}
                                        @else
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <style>
      button.dt-button.buttons-excel.buttons-html5 {
          margin-top: 8px;
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
