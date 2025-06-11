<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESD Vat Report</title>
    <style>
        
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
            margin: 0;
    padding: 0;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            margin: auto;
            font-size: 11px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box *{
            font-size: 12px;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 3px;
            vertical-align: top;
        }

        .invoice-box table tr td:last-child {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            border-bottom: 1px solid #6d6d6d;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item:last-child {
            border-bottom: 1px solid #6d6d6d;
        }

        .invoice-box table tr.item td, .invoice-box table tr.item th {
            border-bottom: 1px solid #6d6d6d;
            border-collapse: collapse;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="2">
                        <h2 style="font-size:18px !important">{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="2" style="    text-align: center;">{{getAllSettings()['ADDRESS_2']}}</td>
                </tr>
                <tr class="top">
                    <!-- <th  colspan="1" style="width: 50%">
                        VAT NO:
                    </th> -->
                    <th colspan="2" style="width: 100%;text-align:center">
                        ESD VAT REPORT
                    </th>
                 

                </tr>
                <tr class="top">
                    <th  colspan="1" style="width: 50%;text-align:left">From Date : {{request()->from ? date('d/m/Y',strtotime(request()->from)) : NULL}}</th>
                   <th  colspan="1" style="width: 50%;text-align:right">To : {{request()->to ? date('d/m/Y',strtotime(request()->to)) : NULL}}</th>
                </tr>
             
            </tbody>        
        </table>
        

            <table >
                <tbody>
                    <tr class="item">
                            <th style="text-align: left">Invoice</th>
                            <td id="invoice_total" style="text-align: right">{{$monthlyinvoices}}</td>                            
                        </tr>
                        <tr class="item">
                            <th style="text-align: left">Cash Sales</th>
                            <td id="cash_sales_total" style="text-align: right">{{$monthlySale}}</td>                            
                        </tr>
                        <tr class="item">
                            <th >Gross Sales </th>
                            <th id="sale_invoice_total" style="text-align: right">{{$sale_invoice_total}}</th>                            
                        </tr>    
                        <tr class="item">
                            <th style="text-align: right"></th>
                            <th id="sale_invoice_total" style="text-align: right"></th>                            
                        </tr>                       
                </tbody>                     
            </table>

            <table >
                <thead>
                    <tr>
                        <th style="text-align: left">Document Type</th>
                        <th style="text-align: left">description</th>
                        <th  style="text-align: left">Vat Rate</th>
                        <th  style="text-align: left">Vat Amount</th>
                        <th  style="text-align: left">Tax Manager</th>
                        <th style="text-align: right">Total Sales With VAT</th>
                    </tr>
                </thead>    
                    <tbody>
                        @foreach ($invoiceData as $item)
                        <tr class="item">
                                <td> Invoice </td>
                                <td>{{$item['description']}}</td>
                                <td>{{($item['vat_rate'])}}</td>
                                <td>{{($item['vat_amount_managed'])}}</td>
                                <td>{{($item['tax_manager_title'])}}</td>
                                <td style="text-align: right">{{($item['total_cost_with_vat_managed'])}}</td>
                        </tr>
                        @endforeach
                        <tr class="item">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th id="grand_total_sales_vat_invoice" style="text-align: right">{{$total_sales_with_vat_invoice}}</th>
                            
                        </tr>
                  
                        <tr class="item">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            
                        </tr>
                        @foreach ($cashSalesData as $item)
                        <tr class="item">
                                <td> Cash Sales </td>
                                <td>{{$item['Cash Sales']}}</td>
                                <td>{{($item['vat_percentage'])}}</td>
                                <td>{{($item['vat_amount_managed'])}}</td>
                                <td>{{($item['tax_manager_title'])}}</td>
                                <td style="text-align: right">{{($item['tax_manager_title'])}}</td>
                        </tr>
                        @endforeach
                        <tr class="item">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th id="grand_total_sales_vat_cash_sales" style="text-align: right">{{$total_sales_with_vat_cash_sales}}</th>
                            
                        </tr>
                        <tr class="item">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr class="item">
                            <th colspan="3" style="text-align: center" id="grand_total_vat">Total VAT : {{$grand_total_vat}}</th>
                            <th colspan="2" style="text-align: right">Total: </th>
                            <th colspan="1" style="text-align: right" id="esd_total">{{$esd_total}}</th>
                        </tr>
                        <tr class="item">
                            <th colspan="5" style="text-align: right" >Unsigned Invoices</th>
                            <th colspan="1" style="text-align: right" id="unsigned_esd">{{$unsigned_esd}}</th>
                        </tr>
                    </tbody>              
            </table>
       

    </div>   
</body>
</html>