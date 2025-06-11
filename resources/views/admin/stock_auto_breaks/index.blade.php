@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Stock Auto Breaks </h3>
                    @if(($user->role_id == 152) & count($records) > 0)
                        <button data-toggle="modal" data-target="#confirm-create-dispatch-modal" data-backdrop="static" class="btn btn-primary">Create Dispatch</button>
                    @endif
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
                            <th>Child</th>
                            <th> Bin</th>
                            <th> Qty</th>
                            <th>Breaks</th>
                            <th>Mother</th>
                            <th> Bin</th>
                            <th> Qty</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($records as $record)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td>{{ $record->child_code }} - {{ $record->child_name }}</td>
                                <td>{{ $record->child_bin }}</td>
                                <td>{{ $record->child_qty }}  {{ $record->child_pack_size }}</td>
                                <td>{{ $record->item_count }}</td>
                                <td>{{ $record->mother_code }} - {{ $record->mother_name }}</td>
                                <td>{{ $record->mother_bin }}</td>
                                <td>{{ $record->mother_qty }}  {{ $record->mother_pack_size }}</td>
                                <td>
                                    <div class="action-button-div">
                                        <a href="{{ route('stock-auto-breaks.lines', $record->child_code) }}" title="View Lines">
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
                            This will create a dispatch sheet at the mother bin for all the current auto breaks in your bin. </p>
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