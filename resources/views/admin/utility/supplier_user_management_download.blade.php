<style>
    /* Add border to table */
    #create_datatable_10 {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid #ddd;
    }

    /* Add border to table header cells */
    #create_datatable_10 th {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    /* Add border to table body cells */
    #create_datatable_10 td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
</style>

<div style="font-weight: 500; font-size: 14; margin-bottom: 10px">Suppliers attached to {{ $users->name }} </div>
<table id="create_datatable_10">
    <thead>
        <tr>
            <th>#</th>
            <th>Suppliers</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($pdfs as $userSuppliers)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $userSuppliers->suppname }}</td>
            
        </tr>
    @endforeach
    </tbody>
</table>
