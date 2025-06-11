@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ request()->child_code }} - Stock Auto Break Lines </h3>
                    <a href="{{ route('stock-auto-breaks.index') }}" class="btn btn-primary"> Back </a>
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
                            <th>Date</th>
                            <th>STB Number</th>
                            <th>Child</th>
                            <th> Bin</th>
                            <th> Qty</th>
                            <th>Mother</th>
                            <th> Bin</th>
                            <th> Qty</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($records as $record)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td>{{ $record->created_at }}</td>
                                <td>{{ $record->stb_number }}</td>
                                <td>{{ $record->child_code }} - {{ $record->child_name }}</td>
                                <td>{{ $record->child_bin }}</td>
                                <td>{{ $record->child_quantity }}  {{ $record->child_pack_size }}</td>
                                <td>{{ $record->mother_code }} - {{ $record->mother_name }}</td>
                                <td>{{ $record->mother_bin }}</td>
                                <td>{{ $record->mother_quantity }}  {{ $record->mother_pack_size }}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection