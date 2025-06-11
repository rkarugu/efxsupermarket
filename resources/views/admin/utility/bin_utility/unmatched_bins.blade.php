<div class="box-header with-border">
    <div class="box-header-flex">
    </div>
</div>
<div style="padding:10px;">
    {{-- <div class="row">
        <form action="{{ route('utility.update_bin') }}" method="get">
            @csrf
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="storeLocation">Location</label>
                            <select name="location" id="storeLocation" class="form-control storeLocation">
                                <option value="">Select Option</option>
                                @foreach ($locations as $index => $location)
                                    <option value="{{ $location->id }}"
                                        {{ $location->id == $location_id ? 'selected' : '' }}>
                                        {{ $location->location_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label style="display:block">&nbsp;</label>
                            <button class="btn btn-primary" type="submit" name="type" value="Filter">
                                <i class="fa fa-filter"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div> --}}

    <table class="table table-bordered table-hover table-striped" id="unmatched_bins_table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th>Item ID</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Store Location</th>
                <th>Bin</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($main_unmatched_bins_data as $item)
                <tr>
                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                    <td>{{ $item->inventory_id }}</td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->item_title }}</td>
                    <td>{{ $item->location?->location_name }}</td>
                    <td>{{ $item->bin?->title }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
