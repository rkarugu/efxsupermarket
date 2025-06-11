@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $dispatch->dispatch_number }} </h3>
                    <a href="{{ route('stock-auto-breaks.dispatch.list') }}" class="btn btn-primary"> Back </a>

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
                            <th> Child Item</th>
                            <th> Child Qty</th>
                            <th> Lines</th>
                            <th> Mother Item</th>
                            <th> Mother Qty</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dispatch->items as $item)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td> {{ $item->child_code }} - {{ $item->child }}</td>
                                <td>{{ $item->child_qty }} {{ $item->child_pack_size }}</td>
                                <td>{{ $item->item_count }}</td>
                                <td> {{ $item->mother_code }} - {{ $item->mother }}</td>
                                <td>{{ $item->mother_qty }} {{ $item->mother_pack_size }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    @php
                        $user = getLoggeduserProfile();
                    @endphp

                    @if($user->wa_unit_of_measures_id == $dispatch->mother_bin_id)
                        <hr>

                        <div class="d-flex justify-content-end">
                            <button data-toggle="modal" data-target="#confirm-create-dispatch-modal" data-backdrop="static" class="btn btn-primary">Process Dispatch</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-create-dispatch-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Dispatch Stock Break </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <p> Are you sure you want to dispatch stock break dispatch {{ $dispatch->dispatch_number }} ? </p>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            <form action="{{ route('stock-auto-breaks.dispatch.process') }}" method="post">
                                {{ @csrf_field() }}
                                <input type="hidden" name="dispatch_id" value="{{ $dispatch->id }}">
                                <button type="submit" class="btn btn-primary">Yes, dispatch</button>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection