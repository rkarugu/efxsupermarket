<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> Item Approval Status </h3>
            </div>
        </div>

        <div class="box-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <th>Status</th>
                    <th>Approval By</th>
                    <th>Date</th>
                </thead>
                <tbody>
                    @foreach ($item->approvalStatus as $status)
                        <tr>
                            <td>{{ $status->status }}</td>
                            <td>{{ $status->approvalBy->name }}</td>
                            <td>{{ date('d M, Y', strtotime($status->created_at)) }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>