<style type="text/css">
    .headers { 
        font-weight: 500; 
        font-size: 11px; 
        text-align: center !important; 
    }
    .table-bordered {
        border-collapse: collapse;
        width: 100%;
    }
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000; /* Define border style */
        padding: 8px; /* Adjust padding for better readability */
    }
    .table-bordered th {
        background-color: #f2f2f2; /* Optional: Define header background color */
    }
</style>
<div style="text-align: left; font-size: 14;font-weight: 500;"> {{ $title }}</div>
<div style="text-align: left; margin-bottom: 5px;"> <strong>Date range: {{ $start_date }} to  {{ $end_date }} </strong></div>

<div class="table-responsive">
    <table class="table table-bordered" id="create_datatable_10">
        <thead>
            <tr>
                <th class="headers">#</th>
                <th class="headers">Date</th>
                <th class="headers">Transfer No</th>
                <th class="headers">Manual Doc No</th>
                <th class="headers">Processed By</th>
                <th class="headers">From Store</th>
                <th class="headers">To Store</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transfers as $transfer)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $transfer->updated_at }}</td>
                <td>{{ $transfer->transfer_no }}</td>
                <td>{{ $transfer->manual_doc_number }}</td>
                <td>{{ $transfer->name }}</td>
                <td>{{ $transfer->location_name }}</td>
                <td>{{ $transfer->too }}</td>
            </tr>
            <tr>
                <td colspan="7">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="headers">Item No</th>
                                <th class="headers">Description</th>
                                <th class="headers">Quantity</th>
                                <th class="headers">Cost</th>
                                <th class="headers">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach ($transfer->getRelatedItem as $item)
                            @php $grandTotal += $item->total_cost; @endphp
                            <tr>
                                <td>{{ $item->getInventoryItemDetail->stock_id_code }}</td>
                                <td>{{ $item->getInventoryItemDetail->title }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->standard_cost, 2) }}</td>
                                <td>{{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="4"><strong>Grand Total</strong></td>
                                <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
