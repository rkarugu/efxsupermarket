@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{!! $title !!}</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['method' => 'get']) !!}
                <div class="row">



                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Bins</label>
                            <select name="store" id="store" class="form-control select2">
                                <option value="" selected>Show All</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" @selected(request()->store == $store->id) }}>{{ $store->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Store</label>
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="" selected>Show All</option>
                                @foreach ($branchs as $branch)
                                    <option value="{{ $branch->id }}" @selected(request()->branch == $branch->id) }}>
                                        {{ $branch->location_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" style="margin-top: 25px; ">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="submit" class="btn btn-primary" name="manage" value="excel">Excel</button>
                        </div>
                    </div>
                </div>
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable_10">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Procurement User</th>
                                <th>Suppliers</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $bins = [];
                            @endphp

                            @foreach ($itemlists as $item)
                                @php
                                    $bin = $item->bin;
                                    if (!array_key_exists($bin, $bins)) {
                                        $bins[$bin] = [];
                                    }
                                    $bins[$bin][] = $item;
                                @endphp
                            @endforeach


                            @foreach ($bins as $bin => $items)
                                <tr class="bin-header">
                                    <td colspan="8"><strong> {{ $bin }}</strong></td>
                                </tr>


                                @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->stock_id_code }}</td>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ $item->subcategory }}</td>
                                        <td>{{ $item->userMAIN }}</td>
                                        <td>{{ $item->supplier }}</td>
                                    </tr>
                                @endforeach
                        </tbody>
                        @endforeach
                    </table>
                    {{ $itemlists->appends(request()->query())->links() }}




                </div>
            </div>
        </div>

    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
@endsection

@section('uniquepagescript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js">
        < script src = "https://code.jquery.com/jquery-3.6.0.min.js" >
    </script>
    <script></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $('body').addClass('sidebar-collapse');
        $(document).ready(function() {
            $('.select2').select2();
        });


        $('#view-issue-modal').on('show.bs.modal', function(event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('issue');

            let date = new Date();
            date.setTime(date.getTime() + (2 * 60 * 1000));
            let expires = "; expires=" + date.toGMTString();

            document.cookie = 'issue' + "=" + dataValue + expires + "; path=/";
        })

        $(document).ready(function() {
            $(".toggle-details").on("click", function() {
                var $detailsRow = $(this).closest("tr").next(".details-row");
                $detailsRow.toggle();
                if ($detailsRow.is(":visible")) {
                    $(this).html('<i class="fas fa-eye-slash"></i>');
                } else {
                    $(this).html('<i class="fas fa-eye"></i>');
                }
            });
        });
    </script>
@endsection
