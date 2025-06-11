@extends('layouts.admin.admin')
@section('content')
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">                        
        @include('message')
        <div class="col-md-12 no-padding-h">
            <form action="" method="get">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="">Date From</label>
                          <input type="date" name="from" value="{{date('Y-m-d')}}" id="from" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="">Date To</label>
                          <input type="date" name="to" value="{{date('Y-m-d')}}" id="to" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4" style="margin-top: 24px;">
                        {{-- <label for="">&nbsp;</label> --}}

                        <button type="submit" class="btn btn-primary" value="filter" name="manage" onclick="filterReport(this); return false;">Filter</button>
                        {{-- <button type="submit" class="btn btn-biz-purplish" value="filter" name="manage" onclick="printPdf(this); return false;"><i class="fa fa-print" aria-hidden="true"></i></button>
                        <button type="submit" class="btn btn-biz-greenish" value="pdf" name="manage"><i class="fa fa-file-pdf"></i></button> --}}
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12 no-padding-h">
            <table class="table table-bordered table-hover tablest" style="display:none;">
                <tr>
                    <th style="width: 80%; align-text:right">Invoice</th>
                    <th id="grand_total_sales_vat_invoice"></th>
                </tr>
                <tr>
                    <th style="width: 80%">Cash Sales</th>
                    <th id="grand_total_sales_vat_cash_sales"></th>
                </tr>

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
                            <th>Total Sales With VAT</th>
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
                            <th colspan="6" style="text-align: center" id="grand_total_vat">0</th>
                        </tr>
                        <tr class="item">
                            <th colspan="5">Unsigned Invoices</th>
                            <th colspan="" style="text-align: right;" id="total_unsigned_invoices">0</th>
                        </tr>
                    </tbody>  
                </table>
            </div>                       
        </div>
    </div>
</section>
@endsection
@section('uniquepagestyle')
<div id="loader-on" style="
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
        function printPdf(input){
            var values = $(input).parents('form').serialize();
            values = values + "&manage=print";
            var url = "{!! route('customer-aging-analysis.esdVatReport') !!}?"+values;
            print_this(url);
        }
        function filterReport(input){
            $('#loader-on').show();
            $('#invoice_body').html('');
            $('#cash_sales_body').html('');
            var values = $(input).parents('form').serialize();
            values = values + "&manage=filter";
            $.ajax({
                type: "GET",
                url: "{!! route('customer-aging-analysis.esdVatReport') !!}?"+values,
                success: function (response) {
                    console.log(response);

                    $('.tablest').show();

                    $.each(response.invoiceData, function (indexInArray, valueOfElement) { 
                        var invoiceTableBody = '<tr>'+
                                '<td> Invoice </td>'+
                                '<td>'+valueOfElement.description+'</td>'+
                                '<td>'+(valueOfElement.vat_rate)+'</td>'+
                                '<td>'+(valueOfElement.vat_amount)+'</td>'+
                                '<td>'+(valueOfElement.tax_manager_title)+'</td>'+
                                '<td>'+(valueOfElement.total_cost_with_vat)+'</td>'+
                        '</tr>';
                        $('#invoice_body').append(invoiceTableBody);
                    });
                    
                    $.each(response.cashSalesData, function (indexInArray2, valueOfElement2) { 
                        var cashSalesTableBody = '<tr>'+
                                '<td> Cash Sales </td>'+
                                '<td>'+valueOfElement2.description+'</td>'+
                                '<td>'+(valueOfElement2.vat_rate)+'</td>'+
                                '<td>'+(valueOfElement2.vat_amount)+'</td>'+
                                '<td>'+(valueOfElement2.tax_manager_title)+'</td>'+
                                '<td>'+(valueOfElement2.total)+'</td>'+
                        '</tr>';
                        $('#cash_sales_body').append(cashSalesTableBody);
                    });


                    $('#grand_total_sales_vat_invoice').html(response.total_sales_with_vat_invoice);
                    $('#grand_total_sales_vat_cash_sales').html(response.total_sales_with_vat_cash_sales);
                    $('#grand_total_vat').html('Total VAT : '+response.grand_total_vat);
                    $('#total_unsigned_invoices').html(response.total_unsigned_invoices);


                    console.log(response.total_sales_with_vat_invoice);
                    console.log(response.total_sales_with_vat_cash_sales);

                    $('#loader-on').hide();
               

                },
                error: function (response) {
                    $('#loader-on').hide();
                }
            });

        }
    </script>
@endsection