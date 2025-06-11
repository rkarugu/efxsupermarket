@extends('layouts.admin.admin')

@section('content')
    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Active Promotions</h3>
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
                            <th>Is Active</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($promotionsGroups as $group)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $group -> name }}</td>
                                <td>{{ $group->active?'Yes':'No' }}</td>
                                <td>{{ $group->start_time }}</td>
                                <td>{{ $group->end_time }}</td>
                                <td>
                                    <a href="{{ route('active-promotions.show', $group) }}" class="btn btn-sm btn-primary"> <i class="fa fa-eye"></i></a>
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
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <style>
        .modal .datepicker {
            z-index: 9999; /* Higher than Bootstrap modal z-index */
        }
        .error-message {
            color: red;
            font-size: 12px;
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
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
        });
    </script>

@endpush
