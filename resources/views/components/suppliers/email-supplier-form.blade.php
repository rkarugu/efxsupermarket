<div class="modal fade" id="send-to-supplier-modal" role="dialog">
    <div class="modal-dialog" style="width: 760px" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">Send Portal Invitation Email</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form action="{!! route('maintain-suppliers.notificationJoinSupplierPortal') !!} " method="post" id="send-to-supplier-form">
                @csrf
                <input type="hidden" id="supplier-id" name="supplier_id">
                <div class="box-body">
                    @csrf
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
    <script>
        $(document).ready(function() {
            $(".tags").tagsinput({
                allowDuplicates: false
            });

            $(".tags").on('beforeItemAdd', function(event) {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                event.cancel = !emailRegex.test(event.item);
            });

            $('#send-to-supplier-modal').on('show.bs.modal', function(event) {
                let button = $(event.relatedTarget);
                $("#supplier-id").val(button.data('id'));

                $('#recipient').tagsinput('removeAll');
                $("#recipient").tagsinput('add', button.data('email'));
                $("#recipient").valid();

                $('#cc').tagsinput('removeAll');
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
    </script>
@endpush
