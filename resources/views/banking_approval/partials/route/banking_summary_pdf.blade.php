<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sales Banking Overview Report</title>
    <style type="text/css">
        /* Reset CSS for PDF */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Basic styling */
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 10px;
        }

        /* Header styling */
        .header {
            text-align: left;
            margin-bottom: 5px;
            /* border-bottom: 2px solid black; */
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }

        .header h2 {
            font-size: 16px;
            margin: 5px 0;
        }

        /* Print info */
        .print-info {
            text-align: right;
            font-size: 9px;
            margin-bottom: 0px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }

        table th {
            background-color: #f8f9fa;
            border-bottom: 1.5px solid black;
            padding: 8px;
            font-weight: bold;
            text-align: left;
        }

        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }

        .amount {
            text-align: right;
        }

        /* Section titles */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            margin: 5px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1.5px solid black;
            color: black;
        }

        /* Signatures section */
        .signatures-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }

        .signatures-grid {
            display: block; 
            width: 100%  !important;
        }

        .signature-row {
            width: 100%  !important;
            display: block;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 23%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: white;
            min-height: 150px;
        }

        .role-title {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #ddd;
        }

        .signature-field {
            margin-bottom: 12px;
        }

        .signature-label {
            font-size: 10px;
            margin-bottom: 3px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 20px;
            margin-bottom: 2px;
        }

        /* Helper classes */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        /* Page break control */
        .page-break {
            page-break-after: always;
        }
    </style>
          <style>
            /* Add this to your existing styles */
            .signature-section {
                margin-top: 20px;
                align-content: center;
                margin-top: 20px;
                width: 100% !important;
                padding: 15px 0; 
                /* display: flex; */
                flex-wrap: wrap; 
                justify-content: space-between;
            }
            
            .signature-row {
                margin-bottom: 20px;
                font-size: 11px;
                line-height: 1.5;
            }
    
            .signature-line {
                display: inline-block;
                border-bottom: 1px dotted #000;
                min-width: 200px;
                margin: 0 10px;
            }
    
            .date-line {
                display: inline-block;
                border-bottom: 1px dotted #000;
                min-width: 150px;
                margin: 0 10px;
            }
    
            .signature-dash {
                display: inline-block;
                border-bottom: 1px dotted #000;
                min-width: 200px;
                margin: 0 10px;
            }
    
            .signature-label {
                font-weight: bold;
            }
        </style>
 
</head>

<body>
    <?php $all_settings = getAllSettings(); ?>
    <div class="header">
        <h3>{{ strtoupper($all_settings['COMPANY_NAME']) }}</h3>
        <h4>CASH SALES BANKING OVERVIEW</h4>
        <h4>BRANCH: {{ is_null($branch) ? 'ALL' : $branch->name }}</h4>
        <p>SALES DATE: {{ $date }}</p>
    </div>
    <div class="print-info">
        Printed By: {{ $user->name }} | Date: {{ date("Y-m-d H:i:s") }}
    </div>
    <div class="section-title">SALES RECONCILIATION</div>
    <table>
        <thead>
            <tr>
                <th style="text-align: right;"> Y Sales </th>
                <th style="text-align: right;"> RTNs </th>
                <th style="text-align: right;"> EAZZY</th>
                <th style="text-align: right;"> EQUITY MAIN</th>
                <th style="text-align: right;"> VOOMA </th>
                <th style="text-align: right;"> KCB MAIN </th>
                <th style="text-align: right;"> MPESA </th>
                <th style="text-align: right;"> TOTAL RCTS </th>
                <th style="text-align: right;"> VERIFIED </th>
                <th style="text-align: right;"> FRAUD JOURNALS </th>
                <th> UNVERIFIED </th>
                <th> Y SALES VARIANCE </th>
                <th> RUNNING BALANCE </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $record)
                <tr>
                    <td class="amount">{{ $record['sales'] }}</td>
                    <td class="amount">{{ $record['returns'] }}</td>
                    <td class="amount">{{ $record['eazzy']}}</td>
                    <td class="amount">{{ $record['equity']}}</td>
                    <td class="amount">{{ $record['vooma']}}</td>
                    <td class="amount">{{ $record['kcb']}}</td>
                    <td class="amount">{{ $record['mpesa']}}</td>
                    <td class="amount">{{ $record['total_receipts']}}</td>
                    <td class="amount">{{ $record['verified_receipts']}}</td>
                    <td class="amount">{{ $record['fraud']}}</td>
                    <td class="amount">{{ $record['variance']}}</td>
                    <td class="amount">{{ $record['debtors_variance']}}</td>
                    <td class="amount">{{ $record['running_balance']}}</td>
                </tr>
                
            @endforeach
          
        </tbody>
    </table>

    <div class="section-title">BANK RECONCILIATION</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th> Account </th>
                <th style="text-align: right;"> Same Day Collections </th>
                <th style="text-align: right;"> Late Utilizations </th>
                <th style="text-align: right;"> Total Collections </th>
                <th style="text-align: right;"> Utilized Unknowns </th>
                <th style="text-align: right;"> Actual Unknowns </th>
                <th style="text-align: right;"> Total Unknowns </th>
                <th style="text-align: right;"> Nominal Total </th>
                <th> Sweep Account </th>
                <th style="text-align: right;"> Sweep Total</th>
                <th style="text-align: right;"> Variance </th>

            </tr>
        </thead>
        <tbody>
            @foreach($summary as $record)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $record->collection_account }}</td>
                <td class="amount">{{ $record->formatted_same_day_collections }}</td>
                <td class="amount">{{ $record->formatted_late_utilizations }}</td>
                <td class="amount">{{ $record->formatted_total_collection }}</td>
                <td class="amount">{{ $record->formatted_utilized_unknowns }}</td>
                <td class="amount">{{ $record->formatted_actual_unknowns }}</td>
                <td class="amount">{{ $record->formatted_total_unknowns }}</td>
                <td class="amount">{{ $record->formatted_nominal_total }}</td>
                <td>{{ $record->sweep_account }}</td>
                <td class="amount">{{ $record->formatted_sweep_total }}</td>
                <td class="amount">{{ $record->formatted_variance }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="section-title">Approvals & Authorizations</div>
    <div class="signature-section" >
        <!-- Row 1 -->
        <div class="signature-row">
            <span class="signature-label" style="margin-left:70px;">Name:</span><span class="signature-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Date:</span><span class="date-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Signature:</span><span class="signature-dash">&nbsp;</span>
        </div>

        <!-- Row 2 -->
        <div class="signature-row">
            <span class="signature-label" style="margin-left:70px;">Name:</span><span class="signature-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Date:</span><span class="date-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Signature:</span><span class="signature-dash">&nbsp;</span>
        </div>

        <!-- Row 3 -->
        <div class="signature-row">
            <span class="signature-label" style="margin-left:70px;">Name:</span><span class="signature-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Date:</span><span class="date-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Signature:</span><span class="signature-dash">&nbsp;</span>
        </div>

        <!-- Row 4 -->
        <div class="signature-row">
            <span class="signature-label" style="margin-left:70px;">Name:</span><span class="signature-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Date:</span><span class="date-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Signature:</span><span class="signature-dash">&nbsp;</span>
        </div>

        <!-- Row 5 -->
        <div class="signature-row">
            <span class="signature-label" style="margin-left:70px;">Name:</span><span class="signature-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Date:</span><span class="date-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Signature:</span><span class="signature-dash">&nbsp;</span>
        </div>

        <!-- Row 6 -->
        <div class="signature-row">
            <span class="signature-label" style="margin-left:70px;">Name:</span><span class="signature-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Date:</span><span class="date-line">&nbsp;</span>
            <span class="signature-label" style="margin-left:70px;">Signature:</span><span class="signature-dash">&nbsp;</span>
        </div>

       
    </div>
</body>
</html>