<style type="text/css">
    .headers {
        font-weight: 500;
        font-size: 11;
        text-align: center !important;
        border: 1px solid #ddd; 
    }
    .table-bordered {
        border-collapse: collapse; 
    }
    .table-bordered td,
    .table-bordered th {
        border: 1px solid #ddd; 
        padding: 8px;
    }
</style>

<div class="table-responsive">
    <table width="100%">
            <tr >
                <td colspan="15" align="left" style="font-weight: 700; font-size: 16px;text-align: left !important">{!!$title!!} Summary
             </td>
            </tr>
        </table> 
  <table class="table table-bordered" id="create_datatable_10">
    <thead>
        <tr style="border-bottom: 1px solid #ddd;
            font-weight: bold;">
            <th class="headers">  <strong>#</strong></th>
            <th class="headers">  <strong>Date</strong></th>
            <th class="headers">  <strong>Transfer No</strong></th>
            <th class="headers">  <strong>Manual Doc No</strong></th>
            <th class="headers">  <strong>Processed By</strong></th>
            <th class="headers">  <strong>From Store</strong></th>
            <th class="headers">  <strong>To Store</strong></th>
            <th class="headers">  <strong>Total Cost</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transfers as $transfer)
            @php
                $grandTotal = 0;
            @endphp
            @foreach ($transfer->getRelatedItem as $item)
                @php
                    $grandTotal += $item->total_cost;
                @endphp
             @endforeach
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $transfer->updated_at }}</td>
                <td>{{ $transfer->transfer_no }}</td>
                <td>{{ $transfer->manual_doc_number }}</td>
                <td>{{ $transfer->name }}</td>
                <td>{{ $transfer->location_name }}</td>
                <td>{{ $transfer->too }}</td>
                <td>{{ number_format($grandTotal, 2) }}</td>
            </tr>
            <tr>
                <table>
                    dcfv
                </table>
                
            </tr>
            
        @endforeach
    </tbody>
</table>