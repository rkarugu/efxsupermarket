<table class="table table-bordered table-hover" id="create_datatable">
    <thead>
        <tr>
            @foreach($titles as $title)
                <th>{{ $title }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
