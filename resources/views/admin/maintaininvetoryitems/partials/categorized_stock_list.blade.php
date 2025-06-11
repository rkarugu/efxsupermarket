@php
    $g_total = 0;
    $g_l_totals = [];
    foreach ($locations as $key => $location) {
        $g_l_totals[$location->id] = 0;
    }
@endphp
@foreach ($items as $category)
    <h3 style="padding: 3px">{{ $category->category_description }}</h3>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>

                <th style="width: 10%">Stock ID Code</th>
                <th style="width: 30%">Title</th>
                <th>Total</th>
                @foreach ($locations as $loc)
                    <th>{{ $loc->location_name }}</th>
                @endforeach
            </tr>
        </thead>
        @php
            $t_t_total = 0;
        @endphp
        <tbody>
            @foreach ($category->getinventoryitems as $item)
                <tr>
                    <td>{{ $item->stock_id_code }}</td>
                    <td>{{ $item->title }}</td>
                    @php
                        $t_total = 0;
                    @endphp
                    @foreach ($locations as $loc)
                        @php
                            $attribute = 'qty_inhand_' . $loc->id;
                            $t_total += $type == 'values' ? $item->$attribute * $item->selling_price : $item->$attribute;
                            $t_t_total += $type == 'values' ? $item->$attribute * $item->selling_price : $item->$attribute;
                        @endphp
                    @endforeach
                    <td class="text-right">{{ manageAmountFormat($t_total) }}</td>
                    @foreach ($locations as $loc)
                        @php $attribute = 'qty_inhand_' . $loc->id; @endphp
                        <td class="text-right">
                            {{ manageAmountFormat($type == 'values' ? $item->$attribute * $item->selling_price : $item->$attribute) }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-center">Totals For: {{ $category->category_description }}</th>
                @php $g_total += $t_t_total @endphp
                <th>{{ manageAmountFormat($t_t_total) }}</th>
                @foreach ($locations as $loc)
                    @php
                        $l_total = 0;
                        $attribute = 'qty_inhand_' . $loc->id;
                    @endphp
                    @foreach ($category->getinventoryitems as $item)
                        @php
                            $l_total += $type == 'values' ? $item->$attribute * $item->selling_price : $item->$attribute;
                        @endphp
                    @endforeach
                    <td class="text-right">{{ manageAmountFormat($l_total) }}</td>
                    @php
                        $g_l_totals[$loc->id] += $l_total;
                    @endphp
                @endforeach
            </tr>
        </tfoot>
    </table>
@endforeach
<table class="table table-bordered" style="margin-top: 20px">
    {{-- <thead>
        <tr>

            <th style="width: 115px"></th>
            <th style="width:300px"></th>
            <th></th>
            @foreach ($locations as $loc)
                <th>{{ $loc->location_name }}</th>
            @endforeach
        </tr>
    </thead> --}}
    <tfoot>
        <tr>
            <th style="width: 10%" class="text-right"></th>
            <th style="width: 30%" class="text-right">Grand Totals</th>
            <th class="text-right">{{ manageAmountFormat($g_total) }}</th>
            @foreach ($locations as $loc)
                <th class="text-right">{{ manageAmountFormat($g_l_totals[$loc->id]) }}</th>
            @endforeach
        </tr>
    </tfoot>
</table>
