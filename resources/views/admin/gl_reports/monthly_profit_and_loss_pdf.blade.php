@php
    $getLoggeduserProfile = getLoggeduserProfile();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Project Summary</title>
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

    <?php $all_settings = getAllSettings();
//  echo print_r($all_settings); die;

?>

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="3">
                        <h2 style="font-size:18px !important">{!! strtoupper($all_settings['COMPANY_NAME'])!!}</h2>
                    </th>
                </tr>
                
                <tr class="top">
                    <!-- <th  colspan="1" style="width: 50%">
                        VAT NO:
                    </th> -->
                    <th colspan="1" style="width: 33%;text-align:left">
                        Start Date: {{date('d-M-Y',strtotime(request()->get('start-date')))}}
                    </th>
                    <th colspan="1"  style="width: 33%;text-align:center">End Date: {{date('d-M-Y',strtotime(request()->get('end-date')))}}</th>
                    <th colspan="1" style="width: 33%;text-align:right">Project: {{@$projects->where('id',request()->project)->first()->title}}</th>

                </tr>
               
            </tbody>        
        </table>

                @if($monthRange<=12)

                <table class="table table-bordered table-hover">
             <?php 
                $logged_user_info = getLoggeduserProfile();
             ?>

       

        <tr class="heading">
            <td style="text-align: left;"><b></b></td>
            @if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))

            @foreach($selectedMonthArr['m'] as $key => $month)

                <td style="text-align: right;"><b>{{getMonthsNameToNumber($month)}}</b></td>
            @endforeach
            @endif
        </tr>
    <!-- Dynamic code start -->

                 <?php 
                $main_qty = [];
                $main_vat = [];
                $main_net = [];
                $main_total = [];

                $new_final_arr=[];
                ?>


                @foreach($gl_tags as $gl_tag)
                 
                        @php $total_stock_arr=[]; @endphp 
                        <tr style="text-align: right;" class="item">
                            <td style="text-align: left;">{{ $gl_tag->title }}</td>
            			@if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                            @foreach($selectedMonthArr['m'] as $key => $month)
                                @php
                                $year=$selectedMonthArr['y'][$key]; 
                                    $created_from=date($year.'-'.$month.'-01');
                                    $created_to=date($year.'-'.$month.'-t');
                                    
                                    $monthlyStock=0;
                                    $monthlyStock=\App\Model\WaGlTran::where('gl_tag',$gl_tag->id)->where(function($e){
                                        if(request()->project){
                                            $e->where('project_id',request()->project);
                                        }
                                    })->whereRaw(\DB::RAW("(CASE WHEN wa_gl_trans.transaction_type = 'Journal' THEN amount > 0 ELSE amount >= 0 OR amount <= 0 END)"))->whereYear('trans_date', $year)->whereMonth('trans_date', $month)->sum('amount'); 
                                    
                                    
                                    $total_stock_arr[]=$monthlyStock;
                                    $new_final_arr[$month.'-'.$year][]=$monthlyStock;
                                    
                                @endphp
                                <td style="text-align: right;">{{manageAmountFormat(abs($monthlyStock))}}</td>
                            @endforeach
                            @endif


                        </tr>
                    @endforeach


                <tr style="text-align: right;">
                    <td colspan="" style="text-align: left;">
                        <b>Total: </b>
                    </td>
                    @if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                            @foreach($selectedMonthArr['m'] as $key => $month)
                                @php
                                $year=$selectedMonthArr['y'][$key]; 
                                @endphp
                    <td>
                            {{manageAmountFormat(array_sum($new_final_arr[$month.'-'.$year] ?? [0.00]))}}
                    </td>
                    @endforeach
                    @endif
                </tr>
            </table>

            @endif
            </div>   
</body>
</html>