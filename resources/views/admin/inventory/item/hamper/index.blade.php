@extends('layouts.admin.admin')

@section('content')
    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $title }}</h3>
                    <div>
                        <a href="{{ route('hampers.create') }}" class="btn btn-primary">{{'+ '}}Create</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <hr>
                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Standard Cost</th>
                            <th>Selling Price</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($hampers as $hamper)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $hamper->title }}</td>
                                <td>{{ $hamper->standard_cost }}</td>
                                <td>{{ $hamper->selling_price }}</td>
                                <td>{{ $hamper->from_date }}</td>
                                <td>{{ $hamper->to_date }}</td>
                                <td>{{ $hamper->status }}</td>
                                <td>
                                    <a href="{{ route('hampers.edit', $hamper) }}" class="btn btn-xs btn-info edit-btn">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </a>

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
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
@endpush
@push('scripts')
    <div id="loader-on"
         style="position: fixed; top: 0; text-align: center; z-index: 999999;
                width: 100%;  height: 100%; background: #000000b8; display:none;"
         class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(function () {

                $(".mlselect").select2();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>

@endpush
