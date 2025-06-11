<div style="padding:10px;">
    <div class="row" style="margin-bottom: 10px">
        <form id="filterForm" action="#" method="get">
            <div class="col-sm-8">
                <div class="row">

                    @php
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

                </div>
            </div>
        </form>
    </div>
    <hr>
    <table class="table table-bordered table-hover table-striped" id="inventory_verify_stocks_table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th>Initiated By</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventoryupdateitemprices as $log)
                @if ($log->status != null)
                    <tr>
                        <td>{{ $log->created_at }}</td>
                        <td>{{ $log?->initiatedby?->name }}</td>
                        <td>{{ $log->item?->stock_id_code }}</td>
                        <td>{{ $log->item?->title }}</td>
                        <td>{{ $log->status }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script></script>
@endpush
