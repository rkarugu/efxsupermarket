@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Dispatched Stock Break Dispatches </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%">#</th>
                            <th>Dispatch Number</th>
                            <th>Date</th>
                            <th> Child Bin</th>
                            <th> Mother Bin</th>
                            <th>Lines</th>
                            <th>Initiated By</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($records as $record)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td>{{ $record->dispatch_number }}</td>
                                <td>{{ $record->created_at }}</td>
                                <td>{{ $record->child_bin }}</td>
                                <td>{{ $record->mother_bin }}</td>
                                <td>{{ $record->item_count }}</td>
                                <td>{{ $record->initiator }}</td>
                                <td>
                                    <div class="action-button-div">
                                        <a href="{{ route('stock-auto-breaks.dispatch.dispatched.lines', $record->id) }}" title="View & Receive">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-create-dispatch-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Create Stock Break Dispatch </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <p> Are you sure you want to initiate a stock break dispatch?
                            This will create a dispatch sheet at the mother bin for all the current auto breaks. </p>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            <form action="{{ route('stock-auto-breaks.dispatch.create') }}" method="post">
                                {{ @csrf_field() }}
                                <button type="submit" class="btn btn-primary">Yes, Initiate</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection