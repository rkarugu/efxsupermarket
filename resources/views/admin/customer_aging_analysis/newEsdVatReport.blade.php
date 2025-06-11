@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">ESD Vat Report</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a>
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Date From</label>
                                    <input type="date" name="from" value="{{ date('Y-m-d') }}" id="from"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Date To</label>
                                    <input type="date" name="to" value="{{ date('Y-m-d') }}" id="to"
                                        class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-4" style="margin-top: 24px;">
                                <button type="submit" class="btn btn-primary" value="filter" name="manage"
                                    onclick="filterReport(this); return false;">Filter</button>
                                {{-- <button type="submit" class="btn btn-biz-purplish" value="filter" name="manage" onclick="printPdf(this); return false;"><i class="fa fa-print" aria-hidden="true"></i></button> --}}
                                {{-- <button type="submit" class="btn btn-biz-greenish" value="pdf" name="manage"><i class="fa fa-file-pdf"></i></button> --}}
                                <button type="submit" class="btn btn-primary" value="pdf"
                                    name="manage">Download</button>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover tablest" id="create_datatable1" style="display: none;">

                        <tbody>
                            <tr class="">
                                <th>Invoice</th>
                                <td id="invoice_total" style="text-align: right"></td>
                            </tr>
                            <tr class="">
                                <th>Cash Sales</th>
                                <td id="cash_sales_total" style="text-align: right"></td>
                            </tr>
                            <tr class="">
                                <th>Gross Sales </th>
                                <th id="sale_invoice_total" style="text-align: right"></th>
                            </tr>
                        </tbody>

                    </table>
                </div>

                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover tablest" id="create_datatable1" style="display: none;">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>description</th>
                                <th>Vat Rate</th>
                                <th>Vat Amount</th>
                                <th>Tax Manager</th>
                                <th style="text-align: right">Total Sales With VAT</th>
                            </tr>
                        </thead>
                        <tbody id="invoice_body"></tbody>
                        <tbody>
                            <tr class="item">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th id="grand_total_sales_vat_invoice" style="text-align: right"></th>

                            </tr>
                        </tbody>
                        <tbody>
                            <tr class="item">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>

                            </tr>
                        </tbody>
                        <tbody id="cash_sales_body"></tbody>

                        <tbody>
                            <tr class="item">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th id="grand_total_sales_vat_cash_sales" style="text-align: right"></th>

                            </tr>
                        </tbody>

                        <tr class="item">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>

                        <tbody>
                            <tr class="item">
                                <th colspan="3" style="text-align: center" id="grand_total_vat">0</th>
                                <th colspan="2" style="text-align: right">Total: </th>
                                <th colspan="1" style="text-align: right" id="esd_total">0</th>
                            </tr>
                            <tr class="item">
                                <th colspan="5" style="text-align: right">Unsigned Invoices</th>
                                <th colspan="1" style="text-align: right" id="unsigned_esd">0</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <div id="loader-on"
        style="
position: fixed;
top: 0;
text-align: center;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
        <div class="loader" id="loader-1"></div>
    </div>
@endsection
@section('uniquepagescript')
    <script>
        function printPdf(input) {
            var values = $(input).parents('form').serialize();
            values = values + "&manage=print";
            var url = "{!! route('customer-aging-analysis.esdVatReport') !!}?" + values;
            print_this(url);
        }

        function filterReport(input) {
            $('#loader-on').show();
            $('#invoice_body').html('');
            $('#cash_sales_body').html('');
            var values = $(input).parents('form').serialize();
            values = values + "&manage=filter";
            $.ajax({
                type: "GET",
                url: "{!! route('customer-aging-analysis.esdVatReport') !!}?" + values,
                success: function(response) {

                    $('.tablest').show();
                    $("#invoice_total").html(response.monthlyinvoices);
                    $("#esd_total").html(response.esd_total);
                    $("#cash_sales_total").html(response.monthlySale);
                    $("#sale_invoice_total").html(response.sale_invoice_total);
                    $("#unsigned_esd").html(response.unsigned_esd);
                    $.each(response.invoiceData, function(indexInArray, valueOfElement) {
                        var invoiceTableBody = '<tr>' +
                            '<td> Invoice </td>' +
                            '<td>' + valueOfElement.description + '</td>' +
                            '<td>' + (valueOfElement.vat_rate) + '</td>' +
                            '<td>' + (valueOfElement.vat_amount_managed) + '</td>' +
                            '<td>' + (valueOfElement.tax_manager_title) + '</td>' +
                            '<td style="text-align: right">' + (valueOfElement
                                .total_cost_with_vat_managed) + '</td>' +
                            '</tr>';
                        $('#invoice_body').append(invoiceTableBody);
                    });

                    $.each(response.cashSalesData, function(indexInArray2, valueOfElement2) {
                        var cashSalesTableBody = '<tr>' +
                            '<td> Cash Sales </td>' +
                            '<td>' + valueOfElement2.description + '</td>' +
                            '<td>' + (valueOfElement2.vat_percentage) + '</td>' +
                            '<td>' + (valueOfElement2.vat_amount_managed) + '</td>' +
                            '<td>' + (valueOfElement2.tax_manager_title) + '</td>' +
                            '<td style="text-align: right">' + (valueOfElement2.total_managed) +
                            '</td>' +
                            '</tr>';
                        $('#cash_sales_body').append(cashSalesTableBody);
                    });


                    $('#grand_total_sales_vat_invoice').html(response.total_sales_with_vat_invoice);
                    $('#grand_total_sales_vat_cash_sales').html(response.total_sales_with_vat_cash_sales);
                    $('#grand_total_vat').html('Total VAT : ' + response.grand_total_vat);


                    $('#loader-on').hide();


                },
                error: function(response) {
                    $('#loader-on').hide();
                }
            });

        }
    </script>
@endsection
