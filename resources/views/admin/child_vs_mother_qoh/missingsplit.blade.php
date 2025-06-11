@extends('layouts.admin.admin')

@section('content')
    <?php
    $user = getLoggeduserProfile();
    $my_permissions = $user->permissions;
    ?>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Mother QOHs > 0 and Child QOH = 0 Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            <div class="box-body">

                @include('message')
                <form action="">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="branch" id="branch" class="form-control">
                                <option value="" selected disabled>Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option 
                                        value="{{ $branch->id }}" 
                                        @if ($branchId == $branch->id) selected @endif
                                    >
                                        {{ $branch->location_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="submit" class="btn btn-primary" value="Filter" style="margin-right: 10px">
                            <input type="submit" class="btn btn-primary" name="manage" value="Excel">
                        </div>
                    </div>
                </form>

                <hr>
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
