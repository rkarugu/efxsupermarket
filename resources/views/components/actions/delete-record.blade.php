<span class="row-action">
    <a href="#" data-record-action="delete" data-toggle="modal" data-target="#{{ $identifier }}" title="delete">
        <i class="fa fa-trash"></i>
    </a>
</span>
<div class="modal fade" tabindex="-1" role="dialog" id="{{ $identifier }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-left">Delete Record</h4>
            </div>
            <form action="{{ $action }}" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <h5 class="text-left"><i class="fa fa-question-circle"></i> Are you sure you want to delete this
                        record?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        &times;
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check-circle"></i>
                        Yes, Delete It!</button>
                </div>
            </form>
        </div>
    </div>
</div>
