<div style="padding:10px;">
    <div class="row" style="margin-bottom: 10px">
        <form id="filterForm" action="#" method="get">
            <div class="col-sm-8">
                <div class="row">
                    @if (!empty($locations_3))
                        <div class="col-sm-3">
                            <label for="locationselect_3">Locations</label>
                            <select name="location" id="locationselect_3" class="form-control">
                                <option value="">Select Option</option>
                                @foreach ($locations_3 as $location)
                                    <option value="{{ $location?->id }}">
                                        {{ $location?->location_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @php
                        $approvedby_3 = $approvedby_3->filter(function ($value) {
                            return !is_null($value);
                        });

                        $initiatedby_3 = $initiatedby_3->filter(function ($value) {
                            return !is_null($value);
                        });
                    @endphp

                    @if (!empty($initiatedby_3))
                        <div class="col-sm-3">
                            <label for="initiatedbyselect_3">Initiated By</label>
                            <select name="initiatedby" id="initiatedbyselect_3" class="form-control">
                                <option value="">Select Option</option>
                                @foreach ($initiatedby_3 as $initiatedb2)
                                    <option value="{{ $initiatedb2->id }}"
                                        {{ request('initiatedby_3') == $initiatedb2->id ? 'selected' : '' }}>
                                        {{ $initiatedb2->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if (!empty($approvedby_3))
                        <div class="col-sm-3">
                            <label for="approvedbyselect_3">Approved By</label>
                            <select name="approvedby" id="approvedbyselect_3" class="form-control">
                                <option value="">Select Option</option>
                                @foreach ($approvedby_3 as $approvedb2)
                                    <option value="{{ $approvedb2->id }}"
                                        {{ request('approvedby_3') == $approvedb2->id ? 'selected' : '' }}>
                                        {{ $approvedb2->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
    <hr>
    <table class="table table-bordered table-hover table-striped" id="inventory_utility_item_prices_table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th>Initiated By</th>
                <th>Location Name</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Standard Cost</th>
                <th>Selling Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventoryupdateitemprices as $log)
                @if ($log?->b_r != null)
                    <tr>
                        <td>{{ $log->created_at }}</td>
                        <td>{{ $log->initiatedby?->name }}</td>
                        <td>{{ $log?->location?->location_name }}</td>
                        <td>{{ $log->item?->stock_id_code }}</td>
                        <td>{{ $log->item?->title }}</td>
                        <td>{{ $log->item?->standard_cost }}</td>
                        <td>{{ $log->locationitemprice?->selling_price }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script></script>
@endpush
