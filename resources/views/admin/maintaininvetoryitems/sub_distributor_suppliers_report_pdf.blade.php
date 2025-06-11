
<table>
    <tr collspan="6" >
        <td collspan="6" style="font-weight: 500;  margin-bottom: 10px;">{{ getAllSettings()['COMPANY_NAME'] }}</td>
    </tr>
        <tr collspan="6" >
            <td collspan="6" style="font-weight: 500;  margin-bottom: 10px;">{{$title}}</td>
        </tr>
</table>
<table class="table table-bordered" id="create_datatable_25">
    <thead>
        <tr>
            <th style="font-weight: 500;">#</th>
            <th style="font-weight: 500;">Main Suppliers</th>
            <th style="font-weight: 500;">Distributors Suppliers</th>
            
        </tr>
    </thead>
    <tbody> 

        @foreach($suppliers as $mainsupplier => $subsuppliers)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $mainsupplier }}</td>
                <td>
                    <ul>
                        @foreach($subsuppliers as $subsupplier)
                            <li>{{ $subsupplier->subsupplier }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endforeach
</tbody>
</table>