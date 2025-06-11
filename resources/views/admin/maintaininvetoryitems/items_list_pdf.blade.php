<style type="text/css">
    .headers {
        font-weight: 500;
        font-size: 11;
        text-align: center !important;
        border: 1px solid #ddd; /* Add border property */
    }

    .table-bordered {
        border-collapse: collapse; /* Collapse borders */
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid #ddd; /* Add border to table cells */
        padding: 8px; /* Add padding for better readability */
    }
</style>

<table>
    <tr collspan="6" >
        <td collspan="6" style="font-weight: 500; font-size: 14px; margin-bottom: 10px;">{{ getAllSettings()['COMPANY_NAME'] }}</td>
    </tr>
        <tr collspan="6" >
            <td collspan="6" style="font-weight: 500; font-size: 14px; margin-bottom: 10px;">ITEM LIST REPORT</td>
        </tr>
</table>
 <table class="table table-bordered" id="create_datatable_10">
    <thead>
        
        <tr class="headers">
            <th><strong># </strong></th>
            <th><strong>Item Code </strong></th>
            <th><strong>Item </strong></th>
            <th><strong>Category </strong></th>
            <th><strong>Sub Category </strong></th>
            <th><strong>Procurement User </strong></th>
            <th><strong>Suppliers </strong></th>
        </tr>
    </thead>
    <tbody>
        @php
            $bins = [];
        @endphp

        @foreach($itemlistPDF as $item)
            @php
                $bin = $item->bin;
                if(!array_key_exists($bin, $bins)) {
                    $bins[$bin] = [];
                }
                $bins[$bin][] = $item;
            @endphp
        @endforeach

        @foreach($bins as $bin => $items)
            <tr class="bin-header">
                <td colspan="5"><strong>{{ $bin }}</strong></td>
            </tr>
            
            @foreach($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->stock_id_code }}</td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->subcategory }}</td>
                    <td>{{ $item->userMAIN }}</td>
                    <td>{{ $item->supplier }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
