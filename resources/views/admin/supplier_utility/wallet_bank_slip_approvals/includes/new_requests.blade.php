<div style="margin-top: 30px">
    <div class="table-responsive">
        <div class="form-group mt-3 text-right" id="action-buttons" style="display:none;">
            <button type="button" class="btn btn-success" id="approve-btn">Approve</button>
            <button type="button" class="btn btn-danger" id="reject-btn">Reject</button>
        </div>
        <table class="table table-bordered" id="new_bank_slips_table">
            <thead>
                <tr>
                    <th style="width: 3%;"><input type="checkbox" id="select-all"></th>
                    <th>SUPPLIER</th>
                    <th>AMOUNT</th>
                    <th>UPLOADED DATE</th>
                    <th>BANK</th>
                    <th>PAYMENT METHOD</th>
                    <th>STATUS</th>
                    <th>FILES</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wallet_slip_approvals_data->filter(fn($record) => $record->status === 'Pending' && $record->approve_status === 'Initial') as $record)
                    <tr>
                        <td>
                            <input type="checkbox" class="select-item" value="{{ $record->id }}"
                                data-wallet-id="{{ $record->wallet_bank_payment_id }}">
                        </td>
                        <td>{{ $record?->tradeagreement?->supplier?->name }}</td>
                        <td>{{ number_format($record?->amount, 2, '.', ',') }}</td>
                        <td>{{ \Carbon\Carbon::parse($record?->uploaded_date)->format('F j, Y') }}</td>
                        <td>{{ $record?->bank }}</td>
                        <td>{{ $record?->payment_method }}</td>
                        <td>{{ $record?->status }}</td>
                        <td>
                            <a href="javascript:void(0);" class="view-files-btn" data-bs-toggle="modal"
                                data-bs-target="#filesModal-{{ $record->id }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            <div class="modal fade" id="filesModal-{{ $record->id }}" tabindex="-1"
                                aria-labelledby="filesModalLabel-{{ $record->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="filesModalLabel-{{ $record->id }}">Bank Slips
                                            </h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                @foreach ($record->walletsupplierdocumentprocessfiles as $file)
                                                    <div class="col-md-4 mb-3">
                                                        <img src="{{ env('SUPPLIER_PORTAL_URI') . '/storage/' . $file->file_path }}"
                                                            alt="Bank Slip" class="img-fluid img-thumbnail">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
