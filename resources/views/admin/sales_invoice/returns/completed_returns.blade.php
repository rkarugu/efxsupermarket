@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Completed Returns </h3>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'completed_returns.index', 'method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-3 form-group">
                        <select name="branch" id="branch" class="form-control mlselect" data-url="{{ route('admin.get-branch-routes') }}">
                            <option value="" selected disabled>Select branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{$branch->id}}" 
                                    {{ request()->has('branch') ? ($branch->id == request()->branch ? 'selected' : '') : ($branch->id == $user->restaurant_id ? 'selected' : '') }}>
                                    {{$branch->name}}
                                </option>

                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-2 form-group">
                        <select name="route" id="route" class="mlselect form-control">
                            <option value="" selected disabled>Select Route</option>
                            @foreach ($routes as $route )
                                <option value="{{$route->id}}" {{ $route->id == request()->route ? 'selected' : '' }}>{{$route->route_name}}</option>

                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        {{-- <input type="submit" class="btn btn-success" name="type" value="Download"> --}}
                        <a class="btn btn-success ml-12" href="{!! route('completed_returns.index') !!}">Clear </a>
                    </div>
                </div>

                {!! Form::close(); !!}

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Date</th>
                                <th>Route</th>
                                <th>Return Number</th>
                                <th>Invoice</th>
                                <th>Amount</th>  
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalReturns = 0;
                            @endphp
                            @foreach ($returns as $return)
                             <tr class="return-row" data-return-id="{{ $return->return_number }}" data-return-date="{{\Carbon\Carbon::parse($return->return_date)->toDateString()}}">
                                <td><i class="fa fa-plus-circle toggle-details" style="cursor: pointer; font-size: 16px;"></i></td>
                                <td>{{ \Carbon\Carbon::parse($return->return_date)->toDateString() }}</td>
                                <td>{{ $return->route }}</td>
                                <td>{{ $return->return_number }}</td>
                                <td>{{ $return->invoice_number }}</td>
                                <td style="text-align: right;">{{ manageAmountFormat($return->total_returns) }}</td>
                             </tr>
                                @php
                                    $totalReturns += $return->total_returns;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">Total</th>
                                <th style="text-align: right;">{{manageAmountFormat($totalReturns)}}</th>
                            </tr>
                        </tfoot>
                       
                        
                    </table>
                    
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

<style>
    .sub-table-amounts {
        text-align: right;
}
.sub-table-qty {
        text-align: center;
}

</style>

@endsection
@section('uniquepagescript')
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $(".mlselect").select2();
    });
</script>
<script>
    $(document).ready(function() {
        $('.toggle-details').on('click', function() {
            var $row = $(this).closest('tr');
            var returnId = $row.data('return-id');
            var returnDate = $row.data('return-date');
            var $icon = $(this);
            var url = '{{ route("completedReturnsDetails", [":return", ":date"]) }}';
            url = url.replace(':return', returnId).replace(':date', returnDate);

            if ($row.next('.return-details').length > 0) {
                $row.next('.return-details').toggle();
                $icon.toggleClass('fa-plus-circle fa-minus-circle');
                return;
            }

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    var detailsRow = '<tr class="return-details"><td colspan="6"><table class="table table-bordered"><thead><tr><th>Stock Id Code</th><th>Title</th><th>Return Qty</th><th>Received Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>';
                    var totalAmount = 0;

                    data.forEach(function(item) {
                        var total = parseFloat(item.received_quantity) * parseFloat(item.selling_price);
                        totalAmount += total;
                        detailsRow += '<tr><td>' + item.stock_id_code + '</td><td>' + item.title + '</td><td class="sub-table-qty">' + item.returned_quantity + '</td><td class="sub-table-qty">' + item.received_quantity + '</td><td class="sub-table-amounts">' + item.selling_price + '</td><td class="sub-table-amounts">' + total.toFixed(2) + '</td></tr>';
                    });

                    detailsRow += '</tbody><tfoot><tr><th colspan="5">Total</th><th class="sub-table-amounts">' + totalAmount.toFixed(2) + '</th></tr></tfoot></table></td></tr>';
                    $row.after(detailsRow);
                    $icon.toggleClass('fa-plus-circle fa-minus-circle');
                },
                error: function() {
                    alert('Error loading return details.');
                }
            });
        });

        $('#branch').change(function() {
                    var branchId = $(this).val();
                    var url = $(this).data('url');
        
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: { branch_id: branchId },
                        success: function(data) {
                            console.log(data);
                            $('#route').empty();
                            $('#route').append('<option value="" selected disabled>Select Route</option>');
        
                            $.each(data.routes, function(key, value) {
                                $('#route').append('<option value="' + value.id + '">' + value.route_name + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });

    });
</script>
@endsection


