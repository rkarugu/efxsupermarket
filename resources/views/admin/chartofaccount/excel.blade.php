<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Sheet</title>
</head>
<body>
    <table>
        <tr>
            <th><b>S. No</b></th>
            <th><b>Account Code</b></th>
            <th><b>Account Name</b></th>
            <th><b>P/L Or B/S</b></th>
            <th><b>Account Section</b></th>
        </tr>
        @foreach ($data  as $key => $row)
        <tr>
            <th colspan="5" style="background-color:#808080;color:#ffffff"><b>{{$row->group_name}}</b></th>
        </tr>
            @foreach ($row->getChartAccount as $key1 => $row1) {
                <tr>
                    <td>{{$key1+1}}</td>
                    <td>{{$row1->account_code}}</td>
                    <td>{{$row1->account_name}}</td>
                    <td>{{$row1->pl_or_bs}}</td>
                    <td>{{$row->getAccountSection->section_name}}</td>
                </tr>
            @endforeach
        @endforeach
    </table>
</body>
</html>