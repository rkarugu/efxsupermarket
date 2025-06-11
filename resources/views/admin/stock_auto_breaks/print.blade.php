<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Auto Break Dispatched</title>
    <style>
       
        .table-bordered {
            border: 1px solid #ddd;
            border-collapse: collapse;
            width: 100%;
        }

        
        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table-bordered th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

      
        .table-bordered tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table-bordered th[style="width: 3%"], .table-bordered td[style="width: 3%"] {
            width: 3%;
        }
    </style>
</head>
<body>
 <?php $all_settings = getAllSettings();
//  echo print_r($all_settings); die;

?>
<div class="table-responsive">
    <div><h3 style="text-align: center;">{{ strtoupper($all_settings['COMPANY_NAME']) }}</h3></div>
    <table class="table table-bordered" id="create_datatable_25">
        <thead>
        <tr>
            <th style="width: 3%">#</th>
            <th>Date</th>
            <th>Item</th>
            <th>Qty</th>
        </tr>
        </thead>
        <tbody>
        @foreach($records as $record)
            <tr>
                <th scope="row">{{ $loop->index + 1 }}</th>
                <td>{{ $record->created_at }}</td>
                <td>{{ $record->title }}</td>
                <td>{{ $record->child_quantity }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
