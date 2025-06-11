@extends('layouts.admin.admin')

@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> Items Without Supplier Report </h3>
            </div>
        </div>

        <div class="box-body">
            {{-- {!! Form::open(['route' => 'inventory-reports.no-supplier-items-report', 'method' => 'get']) !!} --}}
            {!! Form::open(['id' => 'filterForm', 'method' => 'GET']) !!}

            <div class="row">
                <div class="col-md-3 form-group">
                    <select name="packsize" id="packsize" class="mlselect">
                        <option value="" selected disabled>Select Pack Size</option>
                        @foreach ($packSizes as $packSize )
                        <option value="{{$packSize->id}}" {{ $packSize->id == $selectedPackSize ? 'selected' : '' }}>
                            {{$packSize->title}}</option>

                        @endforeach
                    </select>

                </div>
                <div class="col-md-3 form-group">
                    <select name="typeOption" id="typeOption" class="mlselect">
                        <option value="" selected disabled>Select Type</option>
                        @foreach ($typeOptions as $typeOption )
                        <option value="{{$typeOption}}" {{ $typeOption == $selectedTypeOption ? 'selected' : '' }}>
                            {{$typeOption}}</option>

                        @endforeach
                    </select>

                </div>


                <div class="col-md-3 form-group">
                    <button type="button" class="btn btn-success" onclick="submitFilterForm()">Filter</button>
                    <button type="button" class="btn btn-success ml-12" onclick="submitDownloadForm()">Download</button>

                    <a class="btn btn-success ml-12"
                        href="{!! route('inventory-reports.no-supplier-items-report') !!}">Clear </a>
                </div>
            </div>

            {!! Form::close(); !!}

            <hr>

            @include('message')


            <div class="col-md-12">
                <table class="table table-bordered table-hover" id="create_datatable_25">
                    <thead>
                        <th>#</th>
                        <th>Stock Id Code</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Pack Size</th>
                        <th>Standard Cost</th>
                        <th>Selling Price</th>
                        <th>QOH</th>

                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                        <tr>
                            <th>{{ $loop->index+1}}</th>
                            <td>{{ $item->stock_id_code}}</td>
                            <td>{{$item->title}}</td>
                            <td>{{$item->sub_category->title ?? ''}}</td>
                            <td>{{$item->pack_size->title ?? ''}}</td>
                            <td>{{ $item->standard_cost}}</td>
                            <td>{{ $item->selling_price}}</td>
                            <td>{{ getItemQoh($item->id) }}</td>


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
<script type="text/javascript">
function submitFilterForm() {
    document.getElementById('filterForm').action = "{{ route('inventory-reports.no-supplier-items-report') }}";
    document.getElementById('filterForm').submit();
}

function submitDownloadForm() {
    document.getElementById('filterForm').action = "{{ route('inventory-reports.no_supplier_items-report.export') }}";
    document.getElementById('filterForm').submit();
}
</script>

@endsection