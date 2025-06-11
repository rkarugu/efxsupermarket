@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Payment Providers </h3>
                    <a href="{{ route("$base_route.create") }}" class="btn btn-primary"> Add provider </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>Provider</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($providers as $index => $provider)
                            <tr>
                                <th style="width: 5%;" scope="row">{{ $index + 1 }}</th>
                                <td>{{ $provider->name }}</td>
                                <td>
                                    <div class="action-button-div">
                                        @if(can('edit', $permissions_module))
                                            <a href="{{ route("$base_route.edit", $provider->id) }}"><i class="fa fa-edit text-primary fa-lg"></i></a>
                                        @endif
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
