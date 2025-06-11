<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            text-transform: uppercase;
        }

        th {
            font-weight: 500;
            font-size: 11px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            font-weight: 400;
            font-size: 11px;
        }

        .header {
            font-weight: 700;
            font-size: 16px;
            text-align: center !important;
            padding: 10px 0;
        }

        .subheader {
            font-weight: 500;
            font-size: 14px;
            text-align: center !important;
            padding: 8px 0;
        }

        .info {
            font-weight: 400;
            font-size: 12px;
            text-align: center !important;
            padding: 6px 0;
        }
    </style>
</head>
<body>
    <div>
        <table>
            <tr>
                <td colspan="6" class="header">{{ getAllSettings()['COMPANY_NAME'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="header">{!!$title!!}</td>
            </tr>
            <tr>
                <td colspan="6" class="subheader">Date Range: {{$start_date}} to {{ $end_date}}</td>
            </tr>
            <tr>
                <td colspan="6" class="info">Max quantity sold in this period {{ $sold }}</td>
            </tr>
        </table>
       

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Code</th>
                    <th>Description</th>
                    <th>Quantity Balance</th>
                    <th>Quantity Sold</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movings as $record)
               
                <tr class="item">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $record->stock_id_code }}</td>
                    <td>{{ $record->title }}</td>
                    <td>{{ $record->qoh }}</td>
                    <td>{{ $record->total_sales }}</td>
                    <td>{{ $record->category_description }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
