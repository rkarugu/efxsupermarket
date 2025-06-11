<div style="padding:10px;">
    <div class="row" style="margin-bottom: 10px">
        <div class="col-sm-8">
            <div class="row">
                <div class="col-sm-3">

                </div>
                <div class="col-sm-3">

                </div>

            </div>
        </div>
    </div>
    <hr>
    <table class="table table-bordered table-hover table-striped" id="approved_requests_table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Initiated By</th>
                <th>Approved By</th>
                <th>SKU Code</th>
                <th>SKU Name</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th>Pack Size</th>
                <th>Price List Cost</th>
                <th>Gross Weight</th>
                <th>Discounts</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sku_requests->filter(fn($sku_request) => $sku_request->status === 'Approved') as $sku_request)
                <tr>
                    <td>{{ $sku_request->created_at }}</td>
                    <td>{{ $sku_request->supplier?->name }}</td>
                    <td>{{ $sku_request->approvedby?->name }}</td>
                    <td>{{ $sku_request->supplier_sku_code }}</td>
                    <td>{{ $sku_request->supplier_sku_name }}</td>
                    <td>
                        @php
                            $categories = json_decode($sku_request->subcategory, true);
                            $category_description =
                                is_array($categories) && isset($categories['description'])
                                    ? $categories['description']
                                    : 'N/A';
                        @endphp
                        {{ $category_description }}
                    </td>
                    <td>{{ $sku_request->subcategory->title }}</td>
                    <td>{{ $sku_request->packsize->title }}</td>
                    <td>{{ $sku_request->price_list_cost }}</td>
                    <td>{{ $sku_request->gross_weight }}</td>
                    <td>
                        {{ implode(', ', json_decode($sku_request->trade_agreement_discount, true) ?? []) }}
                    </td>
                    <td>{{ $sku_request->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script></script>
@endpush
