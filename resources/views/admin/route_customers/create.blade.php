@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add Route Customer </h3>

                    <a href="{{ route("$base_route_name.index") }}" class="btn btn-outline-primary"> << Back to Route Customers </a>
                </div>
            </div>
        </div>

        <div class="box-body">
            <div class="session-message-container">
                @include('message')
            </div>

            <form action="{{ route("$base_route_name.store") }}" method="post" class="form-horizontal">
                {{ csrf_field() }}
            </form>
        </div>
    </section>
@endsection
