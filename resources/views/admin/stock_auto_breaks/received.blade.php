@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Stock Break Completed </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable_25">
                        <thead>
                        <tr>
                            <th style="width: 3%">#</th>
                            <th>Received time</th>
                            <th> Child Bin</th>
                            <th> Mother Bin</th>
                            <th>Lines</th>
                            <th>Initiated By</th>
                            <th>Dispatched By</th>
                            <th>Received By</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($records as $record) 
                         <tr>
                                <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td>{{ $record->receive_time }}</td>
                                <td>{{ $record->child_bin }}</td>
                                <td>{{ $record->mother_bin }}</td>
                                <td>{{ $record->item_count }}</td>
                                <td>{{ $record->initiator }}</td>
                                <td>{{ $record->dispatcheby }}</td>
                                 <td>{{ $record->received_by_name }}</td>
                                 <td>
                                   <div class="action-button-div">
                                        <a href="{{ route('stock-auto-breaks.dispatch.print',$record->id) }}" title="PDF">
                                            <i class="fa fa-file-pdf" style="color: red"></i>
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

       
    </section>
@endsection