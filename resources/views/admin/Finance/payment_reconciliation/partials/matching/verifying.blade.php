<div class="table-responsive"  style="padding:10px">     
    <table class="table table-bordered" id="verifyingMatchingTable">
        <thead>
            <tr>
                <th>Route</th>
                <th>Channel</th>
                <th>Trans Date</th>
                <th>Doc. No</th>
                <th>Reference</th>
                <th>Bank Ref</th>
                <th>Amount</th>
                <th><input type="checkbox" id="checkAllRecon" checked></th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;padding-right:10px;"><b>Total</b></td>
                <td><b id="verifyingMatchingTotal">{{manageAmountFormat($allMatchData->sum('amount'))}}</b></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <div style="margin-bottom: 10px;clear: both;">
        <div style="float: right;">
            <button type="button" class="btn btn-primary" id="verifyRecon"  style="margin-left:10px;">Verify</button> <br>
            <span id="verifyCheckError" style="color: red;font-size:12px;"></span>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmVerifyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title" id="verifyModalTitle"> Verify Transactions</h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form id="updateVerifyForm" action="" method="POST">
                @csrf
                <div class="box-body">
                    Are you sure You want to Verify these Transactions?
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="confirmVerifyBtn" class="btn btn-primary" data-dismiss="modal">Verify</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#checkAllRecon').on('click', function() {
                console.log('');
                var isChecked = $(this).prop('checked');
                $('#verifyingMatchingTable tbody .matchCheck').prop('checked', isChecked);
                $('#verifyingMatchingTable').DataTable().rows().nodes().to$().find('.matchCheck').prop('checked', isChecked);
            });

            $('#verifyingMatchingTable tbody').on('change', '.matchCheck', function() {
                $('#checkAllRecon').prop('checked', $('.matchCheck:checked').length === $('.matchCheck').length);
            });

            $('#verifyRecon').click(function(){
                var checkboxValues = [];
                $('#verifyingMatchingTable').DataTable().rows().nodes().to$().find('.matchCheck').each(function() {
                    if ($(this).is(":checked")) {
                        checkboxValues.push($(this).val());
                    }
                });
                
                if(!checkboxValues.length){
                    $('#verifyCheckError').html("Select Transactions to Verify");
                    return;
                }
                $('#confirmVerifyModal').modal();
            });

            $('#confirmVerifyBtn').on('click', function (e) {
                e.preventDefault();
                // $('#updateVerifyForm').get(0).submit();
                var checkboxValues = [];
                $('#verifyingMatchingTable').DataTable().rows().nodes().to$().find('.matchCheck').each(function() {
                    if ($(this).is(":checked")) {
                        checkboxValues.push($(this).val());
                    }
                });
                
                if(!checkboxValues.length){
                    $('#verifyCheckError').html("Select Transactions to Verify");
                    return;
                }
                var reconJson = [];
                var newData =[];
                
                var postData = {
                    reconJson: checkboxValues, 
                    channel: $('select[name=channel]').val(),
                    date: $('input[name=date]').val(),
                    branch: $('select[name=branch]').val(),
                };
                $.ajax({
                    url: "{{route('payment-reconciliation.verification.store',$payment->id)}}", 
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    data: postData, 
                    success: function(response) {                                        
                        location.reload(true)
                    },
                    error: function(xhr, status, error) {
                        // Handle error response here
                        console.error(xhr.responseText);
                    }
                });
            });

            $("#verifyingMatchingTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-reconciliation.verification.matching.datatable',$payment->id) !!}',
                    data: function(data) {
                        data.type = 'verfying';
                    }
                },
                columns: [
                    {
                        data: 'debtor.customer_detail.customer_name',
                        name: 'debtor.customerDetail.customer_name',
                        orderable: false,
                    },
                    {
                        data: 'debtor.channel',
                        name: 'debtor.channel'
                    },
                    {
                        data: 'debtor.trans_date',
                        name: 'debtor.trans_date'
                    },
                    {
                        data: 'document_no',
                        name: 'document_no',
                        orderable: false,
                    },
                    {
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'bank_verification.reference',
                        name: 'bankVerification.reference'
                    },     
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false
                    }
                   
                ],
                columnDefs: [
                    {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                return '<input type="checkbox" class="matchCheck" name="reconChecked[]" value="'+row.id+'" checked>';
                            }
                            return data;
                        }
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#verifyingMatchingTotal").text(json.total);
                }
            });
        });
    </script>
@endpush