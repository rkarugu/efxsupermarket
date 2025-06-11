<a href="{{ route('bank-files.download', $file) }}" data-toggle="tooltip" title="Bank File" target="_blank">
    <i class="fa fa-download"></i>
</a>
@if ($file->supportingDocumentRequired() && $file->account->account_name == 'KENYA COMMERCIAL BANK')
    <a href="{{ route('bank-files.supporting-document', $file) }}" style="margin-left: 5px" data-toggle="tooltip"
        title="Supporting Document" target="_blank">
        <i class="fa fa-file-pdf"></i>
    </a>
@endif
@if (can('edit', 'bank-files'))
    <a href="{{ route('bank-files.edit', $file) }}" style="margin-left:5px" data-toggle="tooltip" title="Edit File"
        target="_blank">
        <i class="fa fa-edit"></i>
    </a>
@endif
