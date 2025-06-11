<div style="padding: 10px">
    <form action="{{ route('maintain-items.update-bin-location') }}" method="post" class="submitMe">
        @csrf

        @php
            $authuser = Auth::user();
            $authuserlocation = $authuser->wa_location_and_store_id;
            $isAdmin = $authuser->role_id == 1;
            $hasPermission = isset($permission['maintain-items___view-all-stocks']);
            $hasEditPermission = isset($permission['maintain-items___edit-bin-location']);
        @endphp

        <input type="hidden" name="inventory_id" value="{{ $item->id }}">
        <input type="hidden" name="stockIdCode" value="{{ $item->stock_id_code }}">
        <div class="col-md-12 no-padding-h">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="3%">S.No.</th>
                        <th width="10%">Store Location</th>
                        <th width="10%">Bin Location</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($bins as $bin)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucfirst($bin->location_name) }}</td>
                            <td>
                                <select name="uom_id[{{ $bin->id }}]" class="form-control">
                                    <option value="" selected disabled>Select Bin Location</option>
                                    @foreach ($bin->bin_locations as $loc)
                                        <option value="{{ $loc->id }}" @selected($bin->uom_id == $loc->id)>
                                            {{ $loc->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach --}}
                    @foreach ($bins as $bin)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucfirst($bin->location_name) }}</td>
                            <td>
                                <select name="uom_id[{{ $bin->id }}]" class="form-control"
                                    @if (!$isAdmin && !$hasPermission && $bin->id != $authuserlocation) disabled @endif>
                                    <option value="" selected disabled>Select Bin Location</option>
                                    @foreach ($bin->bin_locations as $loc)
                                        <option value="{{ $loc->id }}" @selected($bin->uom_id == $loc->id)>
                                            {{ $loc->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        @if ($isAdmin || $hasEditPermission)
            <button type="submit" class="btn btn-primary">Update Bin Location</button>
            @else
            <p style="color: white">#</p>
        @endif
    </form>
</div>
