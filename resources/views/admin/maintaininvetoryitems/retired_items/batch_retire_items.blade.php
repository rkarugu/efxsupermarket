@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Batch Retire Items</h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('admin.utility.batch.retire.items.upload') }}" method="post"
                    enctype="multipart/form-data">
                    {{ @csrf_field() }}

                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="cleanup_list" class="control-label"> Items List </label>
                            <input type="file" class="form-control" name="cleanup_list" id="cleanup_list">
                            <label class="custom-file-label" id="cleanup_list_label"></label>
                        </div>

                        <div class="form-group col-sm-3">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" name="intent" id="upload" value="Upload">
                                <i class="fa fa-upload"></i> Upload
                            </button>
                            <button type="submit" class="btn btn-primary" name="intent" id="template" value="Template">
                                <i class="fa fa-file-excel"></i> Template
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                @if ($processingUpload)
                    <form action="{{ route('admin.utility.batch.retire.items.store') }}" method="post">
                        {{ @csrf_field() }}

                        <input type="hidden" name="records" value="{{ json_encode($items) }}">

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 3%;">#</th>
                                        <th>Stock ID Code</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Selling Price</th>
                                        <th>Suppliers</th>
                                        <th>QOH</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                            <td>{{ $item['stock_id_code'] }}</td>
                                            <td>{{ $item['title'] }}</td>
                                            <td>{{ $item['category'] }}</td>
                                            <td>{{ $item['selling_price'] }}</td>
                                            <td>{{ $item['suppliers'] }}</td>
                                            <td>{{ $item['qoh'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            <input type="submit" value="Confirm Retire" class="btn btn-primary">
                        </div>
                    </form>
                @endif

                @if (session('errorItems'))
                    <div class="table-responsive">
                        <h4 class="text-red">These Items were not Retired , They Have QOH</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Stock ID Code</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Selling Price</th>
                                    <th>QOH</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (session('errorItems') as $item)
                                    <tr>
                                        <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                        <td>{{ $item['stock_id_code'] }}</td>
                                        <td>{{ $item['title'] }}</td>
                                        <td>{{ $item['category'] }}</td>
                                        <td>{{ $item['selling_price'] }}</td>
                                        <td>{{ $item['qoh'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $('#cleanup_list').on('change', function() {
            let fileName = $(this).val();
            $(this).next('#cleanup_list_label').text(fileName);
        })
    </script>
@endsection
