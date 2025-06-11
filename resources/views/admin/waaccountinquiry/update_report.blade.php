@extends('layouts.admin.admin')
@section('content')
   
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> GL Transaction Account Update</h3>
                    
                </div>
            </div>
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    @php
                        $account_codes =  getChartOfAccountsList();
                    @endphp
                    <table class="table table-bordered" id="reportTable">
                        <thead>
                            <tr>
                                <th>Transaction No.</th>
                                <th>New Account</th>
                                <th>Old Account</th>
                                <th>Date</th>
                                <th>Created By</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report->glTrans->transaction_no }}</td>
                                <td>{!! isset($account_codes[$report->new_account]) ? $account_codes[$report->new_account] : '' !!} ({!! $report->new_account !!})</td>
                                <td>{!! isset($account_codes[$report->old_account]) ? $account_codes[$report->old_account] : '' !!} ({!! $report->old_account !!})</td>
                                <td>{{ date('Y-m-d H:i', strtotime($report->created_at)) }}</td>
                                <td>{{ $report->createdBy->name }}</td>
                                <td>{{ manageAmountFormat($report->glTrans->amount) }}</td>
                            </tr>
                        @endforeach
                    </table>
                    
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#reportTable').DataTable({
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