@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <form action="{{ route('withholding-files.store') }}" method="POST" id="withHoldingFileForm">
            @csrf
            <div class="box box-primary">
                <div class="box-header with-border">
                    @include('message')
                    <div class="row">
                        <div class="col-sm-9">
                            <h4 class="box-title">Create Withholding File</h4>
                        </div>
                        <div class="col-sm-3">
                            <table class="table table-condensed" style="margin: 0">
                                <tr>
                                    <td class="text-center" style="width: 100px"><strong>TOTAL:</strong></td>
                                    <td class="text-right"><h4 style="margin: 0"><span id="totalAmount">0.00</span></h4></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="filesTable">
                        <thead>
                            <tr>
                                <th>File No</th>
                                <th>Account</th>
                                <th>Items</th>
                                <th>Prepared By</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">W/H Amount</th>
                                <th class="text-center">Donwload</th>
                                <th class="text-center"><input type="checkbox" id="selectAll"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($files as $file)
                                @if (($withholdingAmount = $file->getWithholdingAmount()) > 0)
                                    <tr>
                                        <td>{{ $file->file_no }}</td>
                                        <td>{{ $file->account->account_name }}</td>
                                        <td>{{ $file->items_count }}</td>
                                        <td>{{ $file?->preparedBy?->name }}</td>
                                        <td class="text-right">{{ manageAmountFormat($file->amount) }}</td>
                                        <td class="text-right">{{ manageAmountFormat($withholdingAmount) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('bank-files.download', $file) }}">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="bank_files[]" class="itemCheckbox"
                                                id="item{{ $file->id }}" data-amount="{{ $withholdingAmount }}"
                                                value="{{ $file->id }}">
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7">No account selected</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="text-right">
                        <a href="{{ route('withholding-files.index') }}" class="btn btn-primary">
                            <i class="fa fa-chevron-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#account").select2();

            $('#selectAll').change(function() {
                if ($(this).is(":checked")) {
                    $('.itemCheckbox').each(function(index, item) {
                        $(this).prop('checked', true);
                    });
                } else {
                    $('.itemCheckbox').each(function(index, item) {
                        $(this).prop('checked', false);
                    });
                }

                calculateAmount()
            });

            $('#filesTable tbody').on('change', '.itemCheckbox', function() {
                if ($('.itemCheckbox:checked').length === $('.itemCheckbox').length) {
                    $('#selectAll').prop('checked', true);

                } else {
                    $('#selectAll').prop('checked', false);
                }

                calculateAmount()
            });
        });

        function calculateAmount() {
            let amount = 0;

            $('.itemCheckbox:checked').each(function(index, item) {
                amount += Number($(this).data('amount'));
            })

            $("#totalAmount").text(amount.formatMoney());
        }
    </script>
@endpush
