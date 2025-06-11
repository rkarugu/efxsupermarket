@if (count($items ?? []))
    @foreach ($items as $item)
        <span class="label label-info" style="margin-right: 5px">
            {{ $item }}
        </span>
    @endforeach
@endif
