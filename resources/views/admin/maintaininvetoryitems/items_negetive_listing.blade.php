@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Inventory -Ve Stock Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">

                <form action="" method="get" role="form">

                    <div class="form-group">
                        <label for="">Branch</label>
                        <select name="branch" class="form-control mlselect">
                            <option value="" selected>Show All</option>
                            @foreach ($branches as $key => $branch)
                                <option value="{{ $key }}" {{ $key == request()->branch ? 'selected' : '' }}>
                                    {{ $branch }}</option>
                            @endforeach
                        </select>
                    </div>



                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="submit" class="btn btn-primary" name="excel" value="1">Excel</button>
                </form>

                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>

                                <th width="10%">Stock ID Code</th>
                                <th width="10%">Title</th>
                                <th width="10%">Category</th>
                                <th width="10%">Pack Size</th>
                                <th width="10%">QOH</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->stock_id_code }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ @$item->getInventoryCategoryDetail->category_description }}</td>
                                    <td>{{ @$item->pack_size->title }}</td>
                                    <td>{{ $item->qty_inhand }}</td>
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
@endsection
