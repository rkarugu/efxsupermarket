@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Child Vs Mother QOH Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    {{-- <h3 class="box-title"> Child Vs Mother QOH</h3> --}}
                    <a href="{{ route('child-vs-mother-download') }}" class="btn btn-primary">Download</a>
                </div>

            </div>

            <div class="box-body">

                @include('message')

                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable_25">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mother Item Code</th>
                                <th>Title</th>
                                <th>Pack Size</th>
                                <th>Selling Price</th>
                                <th>Qoh</th>


                                <th>Child Item Code</th>
                                <th>Title</th>
                                <th>Pack Size</th>
                                <th>Selling Price</th>
                                <th>Qoh</th>
                                <th>Factor</th>


                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($inventoryItems as $item)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $item->parent_stock_id }}</td>
                                    <td>{{ $item->parent_title }}</td>
                                    <td>{{ $item->parent_pack_title }}</td>
                                    <td>{{ manageAmountFormat($item->parent_selling_price) }}</td>
                                    <td>{{ $item->parent_quantity }}</td>

                                    <td>{{ $item->child_stock_id }}</td>
                                    <td>{{ $item->child_title }}</td>
                                    <td>{{ $item->child_pack_title }}</td>
                                    <td>{{ manageAmountFormat($item->child_selling_price) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->conversion_factor, 0) }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>



    <script type="text/javascript" class="init">
        $(document).ready(function() {
            $('#create_datatable1').DataTable({
                pageLength: "100",
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
@endsection
