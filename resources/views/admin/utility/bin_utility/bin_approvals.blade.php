<div class="box-header with-border">
    <div class="box-header-flex">
    </div>
</div>
<div style="padding:10px;">
    <div class="row" style="padding-bottom:5px;">
        <div class="col-sm-12 text-right">
            @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___approve-bin-allocation']))
                <form id="confirm_update_bins_form" action="{{ route('utility.approve_update_bin_location') }}"
                    method="post" id="approveBinsForm">
                    @csrf
                    <input type="hidden" name="location" value="{{ request()->location }}">
                    <label style="display:block">&nbsp;</label>
                    <button class="btn btn-primary update_bins_btn" type="submit" id="update_bins" name="type"
                        value="Confirm Update Bins">
                        <i class="fa-solid fa-pen-to-square"></i> Confirm Update Bins
                    </button>
                </form>
            @endif

        </div>

    </div>

    <table class="table table-bordered table-hover table-striped" id="pending_approvals_table">
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
            @foreach ($main_pending_approval_bins_data as $item)
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
