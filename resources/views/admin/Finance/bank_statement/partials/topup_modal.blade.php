<div class="modal fade" id="topUpModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Top up Statements</h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form id="topUpForm" action="{{ route('payment-reconciliation.verification.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    <form id="fetchPaymentForm" action="" method="post">
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label for="equity_makongeni" class="control-label"> Equity Makongeni </label>
                                <input type="file" class="form-control" name="equity_makongeni" id="equity_makongeni">
                                <small class="text-danger" id="equity_makongeni_label" style="height:30px; !important"></small>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="equity_main" class="control-label"> Equity Main </label>
                                <input type="file" class="form-control" name="equity_main" id="equity_main">
                                <small class="text-danger" id="equity_main_label"></small>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="vooma" class="control-label"> Vooma </label>
                                <input type="file" class="form-control" name="vooma" id="vooma">
                                <small class="text-danger" id="vooma_label"></small>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="kcb_main" class="control-label"> KCB Main </label>
                                <input type="file" class="form-control" name="kcb_main" id="kcb_main">
                                <small class="text-danger" id="kcb_main_label"></small>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="mpesa" class="control-label"> Mpesa </label>
                                <input type="file" class="form-control" name="mpesa" id="mpesa">
                                <small class="text-danger" id="mpesa_label"></small>
                            </div>
                        </div>

                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <input type="hidden" name="" id="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="confirmTopUpBtn" class="btn btn-primary" data-id="0" data-dismiss="modal">Top Up</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#equity_makongeni').change(function () {
                var fileName = $(this).val().split('\\').pop();
                $('#equity_makongeni_label').removeClass('text-danger');
                $('#equity_makongeni_label').text(fileName);
            });
            $('#equity_main').change(function () {
                var fileName = $(this).val().split('\\').pop();
                $('#equity_main_label').removeClass('text-danger');
                $('#equity_main_label').text(fileName);
            });
            $('#vooma').change(function () {
                var fileName = $(this).val().split('\\').pop();
                $('#vooma_label').removeClass('text-danger');
                $('#vooma_label').text(fileName);
            });
            $('#kcb_main').change(function () {
                var fileName = $(this).val().split('\\').pop();
                $('#kcb_main_label').removeClass('text-danger');
                $('#kcb_main_label').text(fileName);
            });
            $('#mpesa').change(function () {
                var fileName = $(this).val().split('\\').pop();
                $('#mpesa_label').removeClass('text-danger');
                $('#mpesa_label').text(fileName);
            });
        $('#confirmTopUpBtn').on('click', function (e) {
                e.preventDefault();
                var errors = 0;
                console.log('Test');

                if (errors == 0) {
                    $(this).prop("disabled", true);
                    $('.btn-loader').show();
                    $('#topUpForm').get(0).submit();
                }

            });
        });
        
    </script>
@endpush