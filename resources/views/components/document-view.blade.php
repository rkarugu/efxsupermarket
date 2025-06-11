<div class="modal fade" id="docPreviewModal" tabindex="-1" role="dialog" aria-labelledby="docPreviewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="docPreviewModalLabel" style="font-size: 14px;font-weight:bold"></h3>
            </div>
            <div class="modal-body">
                <iframe src="" width="100%" height="600px" style="border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("body").on('click', '[data-toggle="document"]', function(e) {
                e.preventDefault()

                let docUrl = $(this).data('url');
                let docTitle = $(this).data('title');

                $('#docPreviewModalLabel').text(docTitle);
                $('#docPreviewModal').modal('show');

                let iframe = $('#docPreviewModal').find('iframe');
                iframe.attr('src', docUrl);
            });

            $("#docPreviewModal").on('hide.bs.modal', function() {
                $('#docPreviewModal').find('iframe').remove();
                $('#docPreviewModal .modal-body').append(
                    '<iframe src="" width="100%" height="600px" style="border: none;"></iframe>');
            })
        })
    </script>
@endpush
