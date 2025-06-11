<div class="box-body">
    <table class="table table-bordered" id="missing-in-system-table">
        <thead>
        <tr>
            <th style="width: 3%;">#</th>
            <th>Bank Date</th>
            <th>Original Ref</th>
            <th>Extracted Ref</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($unknown_bankings as $trans)
            <tr>
                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                <td> {{ $trans->bank_date }} </td>
                <td> {{ $trans->original_reference }} </td>
                <td> {{ $trans->reference }} </td>
                <td style="text-align: right;"> {{ manageAmountFormat($trans->amount) }} </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4"></td>
            <th style="text-align: right;"> {{ manageAmountFormat($unknown_bankings_total)}} </th>
        </tr>
        </tfoot>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#missing-in-system-table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
        });
    </script>
@endpush