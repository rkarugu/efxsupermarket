@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{!! $title !!}</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a>
                </div>
            </div>

            {{-- <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!}</h3>
                </div>
            </div> --}}

            <div class="box-body">
                {!! Form::open(['method' => 'get']) !!}

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Store</label>
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="" selected>Show All</option>
                                @foreach ($branches as $branch)
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
                    <table class="table table-bordered" id="create_datatable_50">
    <thead>
        <tr>
            <th>#</th>
            <th>Stock ID Code</th>
            <th>Item</th>
            <th>Category</th>
            <th>Sub Category</th>
            <th>SOH</th>
            <th>Standard Cost</th>
            <th>Selling Price</th>
            <th>Purchasing Data Set</th>
            <th>Last Purchase Date</th>
            <th>Action</th>                
        </tr>
    </thead>
    <tbody> 
      @foreach($itemlists as $item)
            @if($item->sup === null)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->stock_id_code }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->subcategory }}</td>
                <td>{{ $item->qoh }}</td>
                <td>{{ number_format($item->standard_cost, 2) }}</td>
                <td>{{ number_format($item->selling_price, 2)}}</td>
                <td>
                    @if($item->sup === null)
                    No 
                    @else($item->sup !== null)
                    Yes
                    @endif
                </td>
                <td>{{ $item->last_purchase }}</td>
                <td>
                    <span>
                    <a title="Edit" href="{{ route('maintain-items.edit', $item->slug) }}"  target="blank" ><img src="{!! asset('assets/admin/images/edit.png') !!}" >
                    </a>
                    </span>
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
    
</table>
 </div>
</div>
</div>
</section>
 
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $('body').addClass('sidebar-collapse');
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
