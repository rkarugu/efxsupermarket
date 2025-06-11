<div class="modal fade" id="send-to-supplier-modal" role="dialog">
    <div class="modal-dialog" style="width: 760px" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Send LPO To Supplier </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form id="send-to-supplier-form">
                <div class="box-body">
                    <input type="hidden" id="supplier-id" name="supplier">
                    <div class="form-group">
                        <div class="row">
                            <label for="recipient" class="col-sm-2">To:</label>
                            <div class="col-sm-10">
                                <input type="text" id="recipient" name="recipient" class="form-control tags">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label for="cc" class="col-sm-2">CC:</label>
                            <div class="col-sm-10">
                                <input type="text" id="cc" name="cc" class="form-control tags">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" class="form-control"></textarea>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            &times; Cancel</button>
                        <button type="submit" class="btn btn-primary" style="margin-left: 5px">
                            <i class="fa fa-message"></i>
                            Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
    <style>
        .bootstrap-tagsinput {
            width: 100%;
        }

        .bootstrap-tagsinput .tag {
            font-size: 13px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".tags").tagsinput({
                allowDuplicates: false
            });

            $(".tags").on('beforeItemAdd', function(event) {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                event.cancel = !emailRegex.test(event.item);
            });

            $('#message').wysihtml5();

            $('#send-to-supplier-modal').on('show.bs.modal', function(event) {
                let supplier = $("#supplier option:selected");

                console.log(supplier);

                $("#supplier-id").val(supplier.val());
                let nameValue = supplier.text();

                $('#recipient').tagsinput('removeAll');
                $("#recipient").tagsinput('add', supplier.data('email'));
                $("#recipient").valid();

                console.log(supplier.data('emails'));

                if (Array.isArray(supplier.data('emails')) && supplier.data('emails').length > 0) {
                    $('#cc').tagsinput('removeAll');
                    supplier.data('emails').forEach(element => {
                        $("#cc").tagsinput('add', element);
                    });
                }

                $("#message").data('wysihtml5').editor.setValue(renderdefaultMessage(nameValue));
            });

            $("#send-to-supplier-form").validate({
                ignore: [],
                rules: {
                    recipient: {
                        required: true,
                    }
                }
            });
        })

        function renderdefaultMessage(supplierName) {
            return `<h4>Dear ` + supplierName + `,</h4>` +
                `<p>Please find attached the current stock quantities for your products at each branch at KANINI HARAKA.</p>`;
        }
    </script>
@endpush
