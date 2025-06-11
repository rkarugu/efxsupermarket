<select name="store" id="store" class="form-control" @disabled(!can('view_all_branches_data', 'employees'))>
    <option value="">Select Store</option>
    @foreach ($stores as $store)
        <option value="{{ $store->id }}"
            {{ request()->has('store') ? ($store->id == request()->store ? 'selected' : '') : ($store->id == auth()->user()->wa_location_and_store_id ? 'selected' : '') }}>
            {{ $store->location_name }}
        </option>
    @endforeach
</select>
