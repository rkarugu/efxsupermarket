<div style="padding:10px;">
    <div class="row" style="margin-bottom: 10px">
        <div class="col-sm-8">
            <div class="row">
                <div class="col-sm-3">
                    <label for="locationselect">Locations</label>
                    <select name="location" id="locationselect" class="form-control">
                        <option value="">Select Option</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ request('location') == $location->id ? 'selected' : '' }}>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="binselect">Bins</label>
                    <select name="bin" id="binselect" class="form-control">
                        <option value="">Select Option</option>
                        @foreach ($bins as $bin)
                            <option value="{{ $bin->id }}" {{ request('bin') == $bin->id ? 'selected' : '' }}>
                                {{ $bin->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @php
                    $approvedby = $approvedby->filter(function ($value) {
                        return !is_null($value);
                    });

                    $initiatedby = $initiatedby->filter(function ($value) {
                        return !is_null($value);
                    });
                @endphp

                @if (!empty($initiatedby))
                    <div class="col-sm-3">
                        <label for="initiatedbyselect">Initiated By</label>
                        <select name="initiatedby" id="initiatedbyselect" class="form-control">
                            <option value="">Select Option</option>
                            @foreach ($initiatedby as $initiatedb)
                                <option value="{{ $initiatedb->id }}"
                                    {{ request('initiatedby') == $initiatedb->id ? 'selected' : '' }}>
                                    {{ $initiatedb->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if (!empty($approvedby))
                    <div class="col-sm-3">
                        <label for="approvedbyselect">Approved By</label>
                        <select name="approvedby" id="approvedbyselect" class="form-control">
                            <option value="">Select Option</option>
                            @foreach ($approvedby as $approvedb)
                                <option value="{{ $approvedb->id }}"
                                    {{ request('approvedby') == $approvedb->id ? 'selected' : '' }}>
                                    {{ $approvedb->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <hr>
    <table class="table table-bordered table-hover table-striped" id="inventory_utility_table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th>Initiated By</th>
                <th>Approved By</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Location Name</th>
                <th>Bin Name</th>
                <th>Status</th>
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
                    <td>{{ $log?->location?->location_name }}</td>
                    <td>{{ $log?->bin?->title }}</td>
                    <td>
                        @if ($log?->pending_approval_status == 'Pending')
                            Pending
                        @elseif ($log?->rejected_bin_status == 'Rejected')
                            Rejected
                        @elseif ($log?->existing_bin_status == 'Existing')
                            Existing
                        @elseif ($log?->approved)
                            Approved
                        @else
                            ' '
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
       
    </script>
@endpush
