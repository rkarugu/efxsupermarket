<div style="padding:10px;">
    <div class="row" style="margin-bottom: 10px">
        <form id="filterForm" action="#" method="get">
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="locationselect_2">Locations</label>
                        <select name="location" id="locationselect_2" class="form-control">
                            <option value="">Select Option</option>
                            @foreach ($locations_2 as $location)
                                <option value="{{ $location?->id }}">
                                    {{ $location?->location_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @php
                        $approvedby_2 = $approvedby_2->filter(function ($value) {
                            return !is_null($value);
                        });

                        $initiatedby_2 = $initiatedby_2->filter(function ($value) {
                            return !is_null($value);
                        });
                    @endphp

                    @if (!empty($initiatedby_2))
                        <div class="col-sm-3">
                            <label for="initiatedbyselect_2">Initiated By</label>
                            <select name="initiatedby" id="initiatedbyselect_2" class="form-control">
                                <option value="">Select Option</option>
                                @foreach ($initiatedby_2 as $initiatedb2)
                                    <option value="{{ $initiatedb2->id }}"
                                        {{ request('initiatedby_2') == $initiatedb2->id ? 'selected' : '' }}>
                                        {{ $initiatedb2->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if (!empty($approvedby_2))
                        <div class="col-sm-3">
                            <label for="approvedbyselect_2">Approved By</label>
                            <select name="approvedby" id="approvedbyselect_2" class="form-control">
                                <option value="">Select Option</option>
                                @foreach ($approvedby_2 as $approvedb2)
                                    <option value="{{ $approvedb2->id }}"
                                        {{ request('approvedby_2') == $approvedb2->id ? 'selected' : '' }}>
                                        {{ $approvedb2->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-sm-3">
                        <label for="statusselect_2">Status</label>
                        <select name="status" id="statusselect_2" class="form-control">
                            <option value="">Select Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}"
                                    {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr>
    <table class="table table-bordered table-hover table-striped" id="inventory_utility_new_items_table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th>Initiated By</th>
                <th>Approved By</th>
                <th>Item Code</th>
                <th>Item Description</th>
                <th>Status</th>
                <th>Duplicate</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventoryupdateitems as $log)
                <tr>
                    <td>{{ $log->created_at }}</td>
                    <td>{{ $log->initiatedby?->name }}</td>
                    <td>{{ $log->approvedby?->name }}</td>
                    <td>{{ $log->item?->stock_id_code }}</td>
                    <td>{{ $log->item?->description }}</td>
                    <td>{{ $log->item?->approval_status }}</td>
                    <td>{{ $log->duplicate ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        
    </script>
@endpush
