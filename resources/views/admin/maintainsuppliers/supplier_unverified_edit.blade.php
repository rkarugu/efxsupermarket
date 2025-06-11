@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Suppliers Edit Requests </h3>
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
                            <th>Requested By</th>
                            <th>Requested Date</th>
                            <th>Supplier Code</th>
                            <th> Supplier Name</th>
                            <th> Supplier Address</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($records as $record) 
                         <tr>
                                <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td>{{ $record->requester }}</td>
                                <td>{{ $record->created_at }}</td>
                                <td>{{ $record->supplier_code }}</td>
                                <td>{{ $record->name }}</td>
                                <td>{{ $record->address }}</td>
                                   <td>
                                    <div class="action-button-div">
                                    <a href="{{ route('maintain-suppliers.supplier_unverified_show_list', $record->supplier_code) }}" title="View & Approve/Reject ">
                                            <i class="fa fa-eye text-primary"></i>
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