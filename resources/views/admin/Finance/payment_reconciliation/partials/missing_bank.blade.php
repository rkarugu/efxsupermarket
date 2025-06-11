<div class="box-body">
    <table class="table table-bordered" id="missing-in-bank-table">
        <thead>
        <tr>
            <th style="width: 3%;">#</th>
            <th>Trans Date</th>
            <th>Route</th>
            <th>Channel</th>
            <th>Doc. No</th>
            <th>Reference</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($missing_in_bank as $trans)
            <tr>
                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                <td> {{ $trans->trans_date }} </td>
                <td> {{ $trans->customer_name }} </td>
                <td> {{ $trans->channel }} </td>
                <td> {{ $trans->document_no }} </td>
                <td> {{ $trans->reference }} </td>
                <td style="text-align: right;"> {{ manageAmountFormat($trans->amount) }} </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6"></td>
            <th style="text-align: right;"> {{ manageAmountFormat($missing_in_bank_total)}} </th>
        </tr>
        </tfoot>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#missing-in-bank-table').DataTable({
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