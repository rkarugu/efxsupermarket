<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Serials</title>
</head>
<body>
    <table class="table table-bordered table-hover" id="create_datatable">
        <thead>
        <tr>
            <th>stock_id</th>
            <th>serial_no</th>
            <th>price</th>
            <th>weight</th>
            <th>value</th>
            <th>new_weight</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($serials) && !empty($serials))
            @foreach($serials as $list)                                         
                <tr>                                             
                    <td>{{ $list->stock_move->stock_id_code }}</td>
                    <td>{{ $list->serial_no }}</td>
                    <td>{{ $list->purchase_price }}</td>
                    <td>{{ $list->purchase_weight }}</td>
                    <td>{{ $list->value}}</td>
                    <td></td>  										
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</body>
</html>