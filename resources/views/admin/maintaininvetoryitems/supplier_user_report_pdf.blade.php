<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Table</title>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px; /* Add margin to avoid starting on the second page */
    }
    .table th, .table td {
        border: 1px solid #dddddd;
        padding: 2px;
    }
    .table th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>

<table class="table">
    <thead>
        <tr>
            <th>User</th>
            <th>No of Suppliers</th>
            <th>Suppliers</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($suppliers as $userName => $userSuppliers)
        <tr>
            <td>{{ $userName }}</td>
            <td>{{ $userSuppliers->count() }}</td>
            <td>
                <ul style="margin: 0; padding-left: 20px;"> <!-- Adjusted margin and padding -->
                    @foreach ($userSuppliers as $supplier)
                        <li>{{ $supplier->suppname }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
