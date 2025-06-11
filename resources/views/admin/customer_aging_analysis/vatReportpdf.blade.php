@php
    $getLoggeduserProfile = getLoggeduserProfile();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Cash Sales</title>
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
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item:last-child {
            border-bottom: 1px solid #eee;
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
                        VAT ANALYSIS SUMMARIZED BY VAT CODE
                    </th>
                 

                </tr>
                <tr class="top">
                    <th  colspan="1" style="width: 50%;text-align:left">From Date : {{request()->from ? date('d/m/Y',strtotime(request()->from)) : NULL}}</th>
                   <th  colspan="1" style="width: 50%;text-align:right">To : {{request()->to ? date('d/m/Y',strtotime(request()->to)) : NULL}}</th>
                </tr>
                {{-- <tr class="top">
                    <th  colspan="2" style="width: 100%;text-align:center">
                    @if (request()->type == 'true')
                        Users with Upload Rights                        
                    @endif
                    @if (request()->type == 'false')
                        Users without Upload Rights                        
                    @endif
                    @if (request()->type == 'All')
                        All Users            
                    @endif
                    </th>
                </tr> --}}
            </tbody>        
        </table>
        

            <table >
                <thead>
                    <tr class="heading">
                        <th>Code</th>
                        <th>Rate</th>
                        <th style="text-align: right">Goods</th>
                        <th style="text-align: right">Vat</th>
                        <th style="text-align: right">Sell Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="item">
                        <td>Zero Rated</td>
                        <td>0</td>
                        <td style="text-align: right">57,716,536.00</td>
                        <td style="text-align: right">0</td>
                        <td style="text-align: right">57,716,536.00</td>
                    </tr> 

                    <tr class="item">
                        <td>VAT 16</td>
                        <td>16</td>
                        <td style="text-align: right">102,837,617.00</td>
                        <td style="text-align: right">16,454,018.72</td>
                        <td style="text-align: right">119,291,635.72</td>
                    </tr>                            
                </tbody>  
                <tfoot>
                    <tr class="item">
                        <th></th>
                        <th></th>
                        <th style="text-align: right">119,051,107.00</th>
                        <th style="text-align: right">16,454,018.72</th>
                        <th style="text-align: right">177,008,171.72</th>
                    </tr>
                </tfoot>                              
            </table>
       

        {{--
                

                <table>
            <thead>
                <tr class="heading">
                    <td >Code</td>
                    <td >Rate</td>
                    <td  style="text-align: right">Goods</td>
                    <td  style="text-align: right">Vat</td>
                    <td  style="text-align: right">Sell Value</td>
                </tr>
            </thead>
            <tbody>
                @php
                    $posto = $saleso = $toto = 0;
                @endphp
                @foreach ($allTrans as $item)
                    <tr class="item">
                        <td>{{@$item->title}}</td>
                        <td>{{@$item->tax_value}}</td>
                        <td style="text-align: right">{{manageAmountFormat($item->posto)}}</td>
                        <td style="text-align: right">{{manageAmountFormat($item->saleso)}}</td>
                        <td style="text-align: right">{{manageAmountFormat($item->toto)}}</td>
                    </tr>
                    @php
                        $posto += $item->posto;
                        $saleso += $item->saleso;
                        $toto += $item->toto;
                    @endphp
                @endforeach
                </tbody>
                <tfoot>
                    <tr class="item"  style="border-top: 1px solid #eee">
                        <td style="border-top: 1px solid #eee"></td>
                        <td style="border-top: 1px solid #eee"></td>
                        <td style="border-top: 1px solid #eee;text-align: right">{{manageAmountFormat($posto)}}</td>
                        <td style="border-top: 1px solid #eee;text-align: right">{{manageAmountFormat($saleso)}}</td>
                        <td style="border-top: 1px solid #eee;text-align: right">{{manageAmountFormat($toto)}}</td>
                    </tr>
                </tfoot>
        </table>


        --}}    
        
    
    </div>   
</body>
</html>